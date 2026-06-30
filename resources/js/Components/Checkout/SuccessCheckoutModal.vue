<script setup>
import { watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

watch(() => props.show, (newVal) => {
    if (newVal) {
        // Beri waktu animasi jalan 3 detik, lalu redirect
        // Asumsi user punya menu/route pembelian atau home jika dashboard belum ready.
        // Di file route.php ada route '/' dan '/products'. Kita arahkan ke root.
        setTimeout(() => {
            router.visit(route('home'));
        }, 3000);
    }
});
</script>

<template>
    <Modal :show="show" max-width="sm" :closeable="false">
        <div class="p-8 flex flex-col items-center justify-center text-center bg-paper rounded-lg">

            <div class="mb-4">
                <span class="t-success-check" :data-state="show ? 'in' : 'out'" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none" class="w-16 h-16 stroke-terracotta stroke-2">
                        <!-- Standard Checkmark -->
                        <path d="M14 24L22 32L34 16" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>

            <h2 class="text-2xl font-serif text-ink mb-2">Pembayaran Berhasil!</h2>
            <p class="text-taupe mb-2">Terima kasih atas pembelian Anda.</p>
            <p class="text-sm text-taupe-light">Mengarahkan ke halaman awal...</p>

        </div>
    </Modal>
</template>

<style scoped>
/* Transitions.dev — Success check */
:root {
  --check-opacity-dur: 500ms;
  --check-rotate-dur: 500ms;
  --check-rotate-from: 80deg;
  --check-bob-dur: 500ms;
  --check-y-amount: 40px;
  --check-blur-dur: 500ms;
  --check-blur-from: 10px;
  --check-path-dur: 500ms;
  --check-path-delay: 80ms;
  --check-ease-out: cubic-bezier(0.22, 1, 0.36, 1);
  --check-ease-opacity: cubic-bezier(0.22, 1, 0.36, 1);
  --check-ease-rotate: cubic-bezier(0.22, 1, 0.36, 1);
  --check-ease-bob: cubic-bezier(0.34, 1.35, 0.64, 1);
  --check-ease-path: cubic-bezier(0.22, 1, 0.36, 1);
}

.t-success-check {
  display: inline-block;
  transform-origin: center;
  opacity: 0;
  will-change: transform, opacity, filter;
}
.t-success-check svg { display: block; overflow: visible; }
.t-success-check svg path {
  /* getTotalLength() for checkmark path M14 24L22 32L34 16 is approx 31.3. Rounding to 32 */
  stroke-dasharray: 32;
  stroke-dashoffset: 32;
}

.t-success-check[data-state="in"] {
  animation:
    t-check-fade   var(--check-opacity-dur) var(--check-ease-opacity) forwards,
    t-check-rotate var(--check-rotate-dur)  var(--check-ease-rotate)  forwards,
    t-check-blur   var(--check-blur-dur)    var(--check-ease-out)     forwards,
    t-check-bob    var(--check-bob-dur)     var(--check-ease-bob)     forwards;
}
.t-success-check[data-state="in"] svg path {
  animation: t-check-draw var(--check-path-dur) var(--check-ease-path) var(--check-path-delay, 0ms) forwards;
}

@keyframes t-check-fade { from { opacity: 0; } to { opacity: 1; } }
@keyframes t-check-rotate {
  from { transform: rotate(var(--check-rotate-from)); }
  to   { transform: rotate(0deg); }
}
@keyframes t-check-blur {
  from { filter: blur(var(--check-blur-from)); }
  to   { filter: blur(0); }
}
@keyframes t-check-bob {
  from { translate: 0 var(--check-y-amount); }
  to   { translate: 0 0; }
}
@keyframes t-check-draw { to { stroke-dashoffset: 0; } }

@media (prefers-reduced-motion: reduce) {
  .t-success-check { animation: none !important; opacity: 1; }
  .t-success-check svg path { animation: none !important; stroke-dashoffset: 0 !important; }
}
</style>