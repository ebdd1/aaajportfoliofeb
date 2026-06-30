<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

// Props
const props = defineProps({
  accept: {
    type: String,
    default: '.pdf,.jpg,.jpeg,.png,.webp,.docx,.zip'
  },
  maxSize: {
    type: Number,
    default: 50 * 1024 * 1024 // 50MB
  },
  uploadType: {
    type: String,
    default: 'certificate'
  },
  modelValue: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'uploaded', 'error', 'parsed'])

// Refs
const fileInput = ref(null)
const dropZone = ref(null)
const isDragging = ref(false)
const isUploading = ref(false)
const uploadProgress = ref(0)
const uploadSpeed = ref(0)
const uploadTimeRemaining = ref(0)
const uploadResult = ref(null)
const errorMessage = ref(null)
const preview = ref(null)
const previewType = ref(null)

// Upload state machine
const uploadState = ref('idle') // idle, validating, uploading, processing, success, failed, paused, canceled, retry

const stateConfig = {
  idle: { color: 'text-taupe', icon: 'upload' },
  validating: { color: 'text-blue-500', icon: 'loader' },
  uploading: { color: 'text-terracotta', icon: 'upload' },
  processing: { color: 'text-blue-500', icon: 'loader' },
  success: { color: 'text-green-500', icon: 'check' },
  failed: { color: 'text-red-500', icon: 'x' },
  paused: { color: 'text-yellow-500', icon: 'pause' },
  canceled: { color: 'text-gray-500', icon: 'x' },
  retry: { color: 'text-terracotta', icon: 'refresh' }
}

// Computed
const currentState = computed(() => stateConfig[uploadState.value] || stateConfig.idle)
const progressPercent = computed(() => Math.round(uploadProgress.value))
const formattedSpeed = computed(() => {
  if (uploadSpeed.value < 1024) return uploadSpeed.value + ' B/s'
  if (uploadSpeed.value < 1024 * 1024) return (uploadSpeed.value / 1024).toFixed(1) + ' KB/s'
  return (uploadSpeed.value / (1024 * 1024)).toFixed(1) + ' MB/s'
})
const formattedTimeRemaining = computed(() => {
  if (uploadTimeRemaining.value <= 0) return ''
  const mins = Math.floor(uploadTimeRemaining.value / 60)
  const secs = uploadTimeRemaining.value % 60
  return mins > 0 ? `${mins}m ${secs}s` : `${secs}s`
})
const canUpload = computed(() => {
  return uploadState.value === 'idle' || uploadState.value === 'failed' || uploadState.value === 'canceled'
})

// File handling
const selectedFile = ref(null)
let abortController = null
let startTime = 0
let uploadedBytes = 0

const handleFileSelect = async (event) => {
  const files = event.target.files || event.dataTransfer?.files
  if (!files || files.length === 0) return

  const file = files[0]
  await processFile(file)
}

const processFile = async (file) => {
  // Reset state
  errorMessage.value = null
  uploadResult.value = null
  preview.value = null
  previewType.value = null
  selectedFile.value = file

  // Validate file
  uploadState.value = 'validating'

  const validationError = validateFile(file)
  if (validationError) {
    errorMessage.value = validationError
    uploadState.value = 'failed'
    emit('error', { code: 'VALIDATION_ERROR', message: validationError })
    return
  }

  // Generate preview
  generatePreview(file)

  // Start upload
  await uploadFile(file)
}

const validateFile = (file) => {
  // Extension check
  const extension = file.name.split('.').pop()?.toLowerCase()
  const allowedExtensions = props.accept.split(',').map(e => e.replace('.', '').trim())

  if (!allowedExtensions.includes(extension)) {
    return `File type not allowed. Supported: ${allowedExtensions.join(', ')}`
  }

  // Size check
  if (file.size > props.maxSize) {
    return `File size exceeds limit. Maximum: ${formatBytes(props.maxSize)}`
  }

  // Empty file check
  if (file.size === 0) {
    return 'File is empty.'
  }

  return null
}

const generatePreview = (file) => {
  const extension = file.name.split('.').pop()?.toLowerCase()
  const imageExtensions = ['jpg', 'jpeg', 'png', 'webp']
  const docExtensions = ['pdf', 'docx', 'doc']

  if (imageExtensions.includes(extension)) {
    previewType.value = 'image'
    const reader = new FileReader()
    reader.onload = (e) => { preview.value = e.target.result }
    reader.readAsDataURL(file)
  } else if (extension === 'pdf') {
    previewType.value = 'pdf'
    // PDF preview would require PDF.js library
    preview.value = null
  } else if (docExtensions.includes(extension)) {
    previewType.value = 'document'
  } else if (extension === 'zip') {
    previewType.value = 'archive'
  } else {
    previewType.value = 'unknown'
  }
}

const uploadFile = async (file) => {
  uploadState.value = 'uploading'
  isUploading.value = true
  startTime = Date.now()
  uploadedBytes = 0
  uploadProgress.value = 0
  uploadSpeed.value = 0
  uploadTimeRemaining.value = 0

  // Create abort controller for cancellation
  abortController = new AbortController()

  try {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('type', props.uploadType)

    // Track upload progress
    const xhr = new XMLHttpRequest()

    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        uploadProgress.value = (e.loaded / e.total) * 100

        // Calculate speed and time remaining
        const elapsedSeconds = (Date.now() - startTime) / 1000
        uploadSpeed.value = Math.round(e.loaded / elapsedSeconds)
        const remainingBytes = e.total - e.loaded
        uploadTimeRemaining.value = Math.round(remainingBytes / uploadSpeed.value)
      }
    })

    xhr.addEventListener('load', () => {
      if (xhr.status >= 200 && xhr.status < 300) {
        const response = JSON.parse(xhr.responseText)
        handleUploadSuccess(response)
      } else {
        try {
          const error = JSON.parse(xhr.responseText)
          handleUploadError(error)
        } catch {
          handleUploadError({ error: { message: 'Upload failed. Server error.' } })
        }
      }
    })

    xhr.addEventListener('error', () => {
      handleUploadError({ error: { message: 'Network error. Please check your connection.' } })
    })

    xhr.addEventListener('abort', () => {
      uploadState.value = 'canceled'
      isUploading.value = false
    })

    xhr.open('POST', route('api.upload'))
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.content)
    xhr.setRequestHeader('Accept', 'application/json')
    xhr.send(formData)

  } catch (error) {
    handleUploadError({ error: { message: error.message || 'Upload failed.' } })
  }
}

const handleUploadSuccess = (response) => {
  isUploading.value = false
  uploadState.value = 'success'
  uploadResult.value = response.data
  uploadProgress.value = 100

  // Emit events
  emit('uploaded', response.data)
  emit('update:modelValue', response.data)

  // If OCR results are included, emit them too
  if (response.data.ocr_result) {
    emit('parsed', response.data.ocr_result)
  }
}

const handleUploadError = (error) => {
  isUploading.value = false
  uploadState.value = 'failed'
  errorMessage.value = error.error?.message || error.message || 'Upload failed. Please try again.'
  emit('error', error.error || { message: errorMessage.value })
}

const cancelUpload = () => {
  if (abortController) {
    abortController.abort()
  }
  uploadState.value = 'canceled'
  isUploading.value = false
}

const retryUpload = () => {
  if (selectedFile.value) {
    uploadFile(selectedFile.value)
  }
}

const removeFile = () => {
  selectedFile.value = null
  uploadResult.value = null
  preview.value = null
  previewType.value = null
  errorMessage.value = null
  uploadState.value = 'idle'
  uploadProgress.value = 0
  if (fileInput.value) fileInput.value.value = ''
  emit('update:modelValue', null)
}

const openFilePicker = () => {
  fileInput.value?.click()
}

// Drag and drop handlers
const handleDragEnter = (e) => {
  e.preventDefault()
  isDragging.value = true
}

const handleDragLeave = (e) => {
  e.preventDefault()
  isDragging.value = false
}

const handleDragOver = (e) => {
  e.preventDefault()
}

const handleDrop = (e) => {
  e.preventDefault()
  isDragging.value = false

  const files = e.dataTransfer?.files
  if (files && files.length > 0) {
    processFile(files[0])
  }
}

// Paste handler
const handlePaste = (e) => {
  const items = e.clipboardData?.items
  if (!items) return

  for (const item of items) {
    if (item.kind === 'file') {
      const file = item.getAsFile()
      if (file) {
        processFile(file)
        break
      }
    }
  }
}

// Keyboard accessibility
const handleKeyDown = (e) => {
  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault()
    openFilePicker()
  }
}

// Format bytes helper
const formatBytes = (bytes) => {
  if (bytes === 0) return '0 B'
  const units = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + units[i]
}

// Lifecycle
onMounted(() => {
  document.addEventListener('paste', handlePaste)
})

onUnmounted(() => {
  document.removeEventListener('paste', handlePaste)
  if (abortController) abortController.abort()
})
</script>

<template>
  <div class="universal-upload">
    <!-- Hidden file input -->
    <input
      ref="fileInput"
      type="file"
      :accept="accept"
      class="sr-only"
      @change="handleFileSelect"
      @keydown="handleKeyDown"
      aria-label="File upload input"
    />

    <!-- Upload Zone -->
    <div
      ref="dropZone"
      :class="[
        'relative border-2 border-dashed rounded-xl p-8 text-center transition-all duration-300 cursor-pointer',
        isDragging
          ? 'border-terracotta bg-terracotta/5 scale-[1.02]'
          : uploadState === 'failed'
            ? 'border-red-300 bg-red-50'
            : uploadState === 'success'
              ? 'border-green-300 bg-green-50'
              : 'border-oat-dark hover:border-terracotta hover:bg-terracotta/5'
      ]"
      @click="openFilePicker"
      @dragenter="handleDragEnter"
      @dragleave="handleDragLeave"
      @dragover="handleDragOver"
      @drop="handleDrop"
      role="button"
      tabindex="0"
      :aria-label="`Upload file. Drag and drop or click to select. Accepted: ${accept}`"
    >
      <!-- Preview State -->
      <div v-if="preview && previewType === 'image'" class="mb-4">
        <img
          :src="preview"
          :alt="selectedFile?.name"
          class="max-h-48 mx-auto rounded-lg shadow-md"
        />
      </div>

      <!-- Document Icon -->
      <div v-else-if="previewType === 'document'" class="mb-4">
        <div class="w-16 h-16 mx-auto rounded-lg bg-blue-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
      </div>

      <!-- Archive Icon -->
      <div v-else-if="previewType === 'archive'" class="mb-4">
        <div class="w-16 h-16 mx-auto rounded-lg bg-yellow-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
          </svg>
        </div>
      </div>

      <!-- PDF Icon -->
      <div v-else-if="previewType === 'pdf'" class="mb-4">
        <div class="w-16 h-16 mx-auto rounded-lg bg-red-100 flex items-center justify-center">
          <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
          </svg>
        </div>
      </div>

      <!-- Upload Icon (Idle State) -->
      <div v-else class="mb-4">
        <div class="w-16 h-16 mx-auto rounded-full bg-terracotta/10 flex items-center justify-center">
          <svg class="w-8 h-8 text-terracotta" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
        </div>
      </div>

      <!-- File Info -->
      <div v-if="selectedFile" class="mb-4">
        <p class="font-medium text-ink truncate max-w-xs mx-auto">{{ selectedFile.name }}</p>
        <p class="text-sm text-taupe">{{ formatBytes(selectedFile.size) }}</p>
      </div>

      <!-- Idle State Text -->
      <div v-else>
        <p class="text-ink font-medium mb-1">
          {{ isDragging ? 'Drop file here' : 'Drag & drop file or click to select' }}
        </p>
        <p class="text-sm text-taupe">
          Supported: PDF, JPG, PNG, DOCX, ZIP. Max: {{ formatBytes(maxSize) }}
        </p>
      </div>
    </div>

    <!-- Progress Bar -->
    <div v-if="uploadState === 'uploading'" class="mt-4">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm text-taupe">Uploading...</span>
        <span class="text-sm font-medium text-ink">{{ progressPercent }}%</span>
      </div>
      <div class="h-2 bg-oat rounded-full overflow-hidden">
        <div
          class="h-full bg-terracotta transition-all duration-300"
          :style="{ width: `${progressPercent}%` }"
        ></div>
      </div>
      <div class="flex justify-between mt-2 text-xs text-taupe">
        <span>{{ formattedSpeed }}</span>
        <span>{{ formattedTimeRemaining }} remaining</span>
      </div>
    </div>

    <!-- Success State -->
    <div v-if="uploadState === 'success' && uploadResult" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
      <div class="flex items-center gap-2 text-green-700">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span class="font-medium">File uploaded successfully!</span>
      </div>
      <p class="text-sm text-green-600 mt-1">File ID: {{ uploadResult.id }}</p>

      <!-- OCR Results -->
      <div v-if="uploadResult.ocr_result && uploadResult.ocr_result.title" class="mt-3 pt-3 border-t border-green-200">
        <p class="text-sm font-medium text-green-800 mb-1">Auto-detected data:</p>
        <div class="text-sm text-green-700 space-y-1">
          <p v-if="uploadResult.ocr_result.title"><strong>Title:</strong> {{ uploadResult.ocr_result.title }}</p>
          <p v-if="uploadResult.ocr_result.issuer"><strong>Issuer:</strong> {{ uploadResult.ocr_result.issuer }}</p>
          <p v-if="uploadResult.ocr_result.issue_date"><strong>Date:</strong> {{ uploadResult.ocr_result.issue_date }}</p>
          <p class="text-xs text-green-600 mt-1">Confidence: {{ Math.round(uploadResult.ocr_result.confidence * 100) }}%</p>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-if="uploadState === 'failed' && errorMessage" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
      <div class="flex items-center gap-2 text-red-700 mb-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="font-medium">Upload failed</span>
      </div>
      <p class="text-sm text-red-600">{{ errorMessage }}</p>
    </div>

    <!-- Action Buttons -->
    <div v-if="selectedFile" class="mt-4 flex gap-3">
      <!-- Cancel Button (during upload) -->
      <button
        v-if="uploadState === 'uploading'"
        type="button"
        @click.stop="cancelUpload"
        class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors"
      >
        Cancel
      </button>

      <!-- Retry Button (after failure) -->
      <button
        v-if="uploadState === 'failed' || uploadState === 'canceled'"
        type="button"
        @click.stop="retryUpload"
        class="flex-1 px-4 py-2 bg-terracotta text-cream rounded-lg hover:bg-terracotta-dark transition-colors flex items-center justify-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Retry Upload
      </button>

      <!-- Remove Button -->
      <button
        v-if="uploadState !== 'uploading'"
        type="button"
        @click.stop="removeFile"
        class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors"
      >
        Remove
      </button>

      <!-- Processing State -->
      <div v-if="uploadState === 'processing'" class="flex-1 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center gap-2">
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Processing...
      </div>
    </div>
  </div>
</template>
