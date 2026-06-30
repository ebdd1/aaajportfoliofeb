<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Navigation from '@/Components/Navigation.vue';
import Footer from '@/Components/Footer.vue';
import SuccessCheckoutModal from '@/Components/Checkout/SuccessCheckoutModal.vue';

const props = defineProps({
    order: Object,
    payment: Object,
    profile: Object,
    socialLinks: Array,
});

const timeLeft = ref('');
const isExpired = ref(false);
const showSuccessModal = ref(false);
let countdownInterval = null;
let pollingInterval = null;

const paymentMethods = {
    'qris': { name: 'QRIS', icon: '📱' },
    'bni_va': { name: 'BNI Virtual Account', icon: '🏦' },
    'bri_va': { name: 'BRI Virtual Account', icon: '🏦' },
    'mandiri_va': { name: 'Mandiri Virtual Account', icon: '🏦' },
    'cimb_niaga_va': { name: 'CIMB Niaga VA', icon: '🏦' },
    'maybank_va': { name: 'Maybank VA', icon: '🏦' },
    'permata_va': { name: 'Permata VA', icon: '🏦' },
};

const paymentInfo = computed(() => {
    const method = props.payment?.payment_method || 'qris';
    return paymentMethods[method] || { name: method, icon: '💳' };
});

const formatPrice = (price) => {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
};

const formatExpiredAt = computed(() => {
    if (!props.order?.expired_at) return null;
    const date = new Date(props.order.expired_at);
    return date.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
});

const qrCodeUrl = computed(() => {
    if (!props.payment?.payment_number) return '';
    return `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(props.payment.payment_number)}`;
});

const updateCountdown = () => {
    if (!props.order?.expired_at) return;

    const now = new Date();
    const expired = new Date(props.order.expired_at);
    const diff = expired - now;

    if (diff <= 0) {
        timeLeft.value = '00:00:00';
        isExpired.value = true;
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
        return;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    timeLeft.value = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
};

const refreshPage = () => {
    router.reload({ only: ['order', 'payment'] });
    isExpired.value = false;
};

watch(() => props.order?.status, (newStatus) => {
    if (newStatus === 'paid') {
        showSuccessModal.value = true;
        // Stop polling and countdown
        if (pollingInterval) clearInterval(pollingInterval);
        if (countdownInterval) clearInterval(countdownInterval);
    }
});

onMounted(() => {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);

    // Auto-polling for status checking if not already paid
    if (props.order?.status !== 'paid') {
        pollingInterval = setInterval(() => {
            router.reload({ only: ['order'] });
        }, 3000); // Poll every 3 seconds
    } else {
        showSuccessModal.value = true;
    }
});

onUnmounted(() => {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});
</script>

<template>
    <div class="min-h-screen bg-cream flex flex-col">
        <Navigation :profile="profile" variant="minimal" />

        <main class="flex-1 py-12">
            <div class="container mx-auto px-4 max-w-xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="font-serif text-3xl font-bold text-ink mb-2">Instruksi Pembayaran</h1>
                <p class="text-taupe">Selesaikan pembayaran sebelum batas waktu</p>
            </div>

            <!-- Order Info -->
            <div class="bg-paper rounded-2xl border border-oat-dark p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-taupe">Order ID</span>
                    <span class="font-mono text-sm font-semibold text-ink">{{ order?.order_id }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-taupe">Total Bayar</span>
                    <span class="text-xl font-bold text-terracotta">{{ formatPrice(order?.total_amount) }}</span>
                </div>
            </div>

            <!-- Countdown Timer -->
            <div class="bg-paper rounded-2xl border border-oat-dark p-6 mb-6 text-center">
                <p class="text-taupe text-sm mb-2">Batas Waktu Pembayaran</p>
                <div v-if="!isExpired" class="font-mono text-4xl font-bold text-ink">
                    {{ timeLeft }}
                </div>
                <div v-else class="text-red-600">
                    <p class="text-xl font-semibold mb-2">Pembayaran Kadaluarsa</p>
                    <button
                        @click="refreshPage"
                        class="px-4 py-2 bg-terracotta text-cream rounded-lg hover:bg-terracotta-dark transition-colors"
                    >
                        Refresh Halaman
                    </button>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-paper rounded-2xl border border-oat-dark p-6 mb-6">
                <h2 class="font-semibold text-ink mb-4 flex items-center gap-2">
                    <span class="text-2xl">{{ paymentInfo.icon }}</span>
                    {{ paymentInfo.name }}
                </h2>

                <!-- QRIS Section -->
                <div v-if="order?.payment_method === 'qris' || !order?.payment_method" class="text-center">
                    <p class="text-taupe text-sm mb-4">Scan QR code berikut dengan aplikasi pembayaran Anda:</p>
                    <div class="bg-white inline-block p-4 rounded-xl border border-oat-dark">
                        <img
                            v-if="qrCodeUrl"
                            :src="qrCodeUrl"
                            alt="QR Code Pembayaran"
                            class="w-64 h-64 mx-auto"
                        />
                        <div v-else class="w-64 h-64 flex items-center justify-center text-taupe">
                            Memuat QR Code...
                        </div>
                    </div>
                    <p class="text-sm text-taupe mt-4">
                        Atau buka aplikasi e-wallet/m-banking dan scan QR code di atas
                    </p>
                </div>

                <!-- Virtual Account Section -->
                <div v-else class="space-y-4">
                    <div class="bg-oat rounded-xl p-4">
                        <p class="text-sm text-taupe mb-1">Nomor Virtual Account</p>
                        <p class="font-mono text-xl font-bold text-ink break-all">{{ order?.payment_number }}</p>
                    </div>
                    <div class="bg-oat rounded-xl p-4">
                        <p class="text-sm text-taupe mb-1">Total Transfer</p>
                        <p class="font-bold text-lg text-terracotta">{{ formatPrice(order?.total_amount) }}</p>
                    </div>
                    <div class="bg-oat rounded-xl p-4">
                        <p class="text-sm text-taupe mb-1">Pastikan transfer sesuai nominal</p>
                        <p class="text-xs text-taupe">Nominal harus sama persis untuk otomatis terverifikasi</p>
                    </div>
                </div>
            </div>

            <!-- Fee Info -->
            <div v-if="payment?.fee" class="bg-oat/50 rounded-2xl border border-oat-dark p-4 mb-6">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-taupe">Subtotal</span>
                    <span class="text-ink">{{ formatPrice(payment.amount) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-taupe">Biaya Layanan</span>
                    <span class="text-ink">{{ formatPrice(payment.fee) }}</span>
                </div>
                <div class="flex justify-between items-center font-semibold mt-2 pt-2 border-t border-oat-dark">
                    <span class="text-ink">Total</span>
                    <span class="text-terracotta">{{ formatPrice(payment.total_payment) }}</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-terracotta/5 rounded-2xl border border-terracotta/20 p-4 mb-6">
                <h3 class="font-semibold text-ink mb-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-terracotta" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="16" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                    Cara Pembayaran
                </h3>
                <ol class="text-sm text-taupe space-y-1 list-decimal list-inside">
                    <li>Buka aplikasi e-wallet atau m-banking Anda</li>
                    <li>Pilih fitur Scan QR / Bayar QRIS</li>
                    <li>Scan QR code di atas atau masukkan nomor VA</li>
                    <li>Masukkan nominal sesuai yang tertera</li>
                    <li>Konfirmasi dan selesaikan pembayaran</li>
                    <li>Halaman ini akan otomatis terverifikasi setelah pembayaran berhasil</li>
                </ol>
            </div>

            <!-- Success Check -->
            <div class="text-center">
                <p class="text-sm text-taupe mb-4">
                    Sudah melakukan pembayaran?
                </p>
                <button
                    @click="refreshPage"
                    class="px-6 py-3 bg-terracotta text-cream font-semibold rounded-xl hover:bg-terracotta-dark transition-colors"
                >
                    Cek Status Pembayaran
                </button>
            </div>

            <!-- Modal Sukses Animasi -->
            <SuccessCheckoutModal :show="showSuccessModal" />
        </div>
        </main>

        <Footer :profile="profile" :social-links="socialLinks" variant="minimal" />
    </div>
</template>
