<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { WalletIcon } from '@heroicons/vue/24/solid';
import { ArrowTrendingUpIcon, ArrowTrendingDownIcon, BanknotesIcon, DocumentTextIcon, FlagIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    wallets: Object,
    summary: Object,
    recentTransactions: Object,
    unpaidInvoices: Object,
    budgetsThisMonth: Object,
    savingsGoals: Object,
    outstandingInvoiceCount: Number,
    outstandingInvoiceAmount: Number,
});

const formatRupiah = (amount) => {
    const num = Number(amount);
    if (isNaN(num)) return 'Rp 0';
    return 'Rp ' + num.toLocaleString('id-ID');
};

const formatDate = (date) => {
    if (!date) return '-';
    const d = new Date(date);
    return d.toLocaleDateString('id-ID');
};

const getStatusClass = (status) => {
    const map = {
        draft: 'bg-cream text-taupe',
        sent: 'bg-blue-100 text-blue-700',
        paid: 'bg-green-100 text-green-700',
        overdue: 'bg-red-100 text-red-700',
        cancelled: 'bg-cream text-taupe',
    };
    return map[status] || map.draft;
}

const walletList = computed(() => props.wallets || []);
const transactionList = computed(() => props.recentTransactions || []);
const invoiceList = computed(() => props.unpaidInvoices || []);
const budgetList = computed(() => props.budgetsThisMonth || []);
const calcPercentage = (current, target) => {
    if (!target || parseFloat(target) === 0) return 0;
    return Math.min(100, (parseFloat(current) / parseFloat(target)) * 100);
};

const goalList = computed(() => props.savingsGoals || []);

const calcBudgetPercentage = (spent, amount) => {
    if (!amount || parseFloat(amount) === 0) return 0;
    return Math.min(100, (parseFloat(spent) / parseFloat(amount)) * 100);
};

const getBudgetColor = (percentage) => {
    if (percentage < 75) return 'bg-green-500';
    if (percentage < 90) return 'bg-yellow-500';
    return 'bg-red-500';
};
</script>

<template>
    <Head title="Dashboard Keuangan" />

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-fraunces font-bold text-ink">Dashboard Keuangan</h1>
            </div>

            <!-- Summary Cards - 4 columns on lg, 2 on md, 1 on sm -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Saldo -->
                <div class="bg-paper border border-oat-dark rounded-2xl p-5 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-terracotta/10 rounded-xl flex items-center justify-center">
                            <WalletIcon class="w-5 h-5 text-terracotta" />
                        </div>
                        <span class="text-sm text-taupe">Total Saldo</span>
                    </div>
                    <p class="text-2xl font-bold text-ink font-variant-numeric tabular-nums">{{ formatRupiah(props.summary?.total_balance) }}</p>
                </div>

                <!-- Pemasukan -->
                <div class="bg-paper border border-oat-dark rounded-2xl p-5 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-500/10 rounded-xl flex items-center justify-center">
                            <ArrowTrendingUpIcon class="w-5 h-5 text-green-600" />
                        </div>
                        <span class="text-sm text-taupe">Pemasukan</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600 font-variant-numeric tabular-nums">{{ formatRupiah(props.summary?.income_this_month) }}</p>
                    <p class="text-xs text-taupe mt-1">vs bulan lalu: {{ formatRupiah(props.summary?.income_last_month) }}</p>
                </div>

                <!-- Pengeluaran -->
                <div class="bg-paper border border-oat-dark rounded-2xl p-5 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center">
                            <ArrowTrendingDownIcon class="w-5 h-5 text-red-600" />
                        </div>
                        <span class="text-sm text-taupe">Pengeluaran</span>
                    </div>
                    <p class="text-2xl font-bold text-red-600 font-variant-numeric tabular-nums">{{ formatRupiah(props.summary?.expense_this_month) }}</p>
                    <p class="text-xs text-taupe mt-1">vs bulan lalu: {{ formatRupiah(props.summary?.expense_last_month) }}</p>
                </div>

                <!-- Invoice Outstanding -->
                <div class="bg-paper border border-oat-dark rounded-2xl p-5 hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-yellow-500/10 rounded-xl flex items-center justify-center">
                            <DocumentTextIcon class="w-5 h-5 text-yellow-600" />
                        </div>
                        <span class="text-sm text-taupe">Invoice</span>
                    </div>
                    <p class="text-2xl font-bold text-ink font-variant-numeric tabular-nums">{{ props.outstandingInvoiceCount || 0 }}</p>
                    <p class="text-sm text-red-600 font-medium font-variant-numeric tabular-nums">{{ formatRupiah(props.outstandingInvoiceAmount) }}</p>
                </div>
            </div>

            <!-- Wallets Section - Grid instead of horizontal scroll -->
            <div class="bg-paper border border-oat-dark rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-fraunces font-semibold text-ink">Dompet</h2>
                    <Link :href="route('admin.finance.wallets.index')" class="text-sm text-terracotta hover:text-terracotta/80 font-medium">Kelola</Link>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <div v-for="wallet in walletList" :key="wallet.id" class="bg-cream rounded-xl p-4 hover:bg-cream/70 transition-colors duration-200">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" :style="{ backgroundColor: (wallet.color || '#ccc') + '20' }">
                                <BanknotesIcon class="w-4 h-4" :style="{ color: wallet.color || '#666' }" />
                            </div>
                            <span class="font-medium text-ink text-sm">{{ wallet.name || '-' }}</span>
                        </div>
                        <p class="text-lg font-bold font-variant-numeric tabular-nums" :style="{ color: wallet.color || '#333' }">{{ formatRupiah(wallet.balance) }}</p>
                        <span class="text-xs text-taupe capitalize">{{ wallet.type || '-' }}</span>
                    </div>
                    <div v-if="walletList.length === 0" class="col-span-full text-center py-8">
                        <p class="text-taupe">Belum ada dompet</p>
                        <Link :href="route('admin.finance.wallets.create')" class="text-terracotta hover:underline text-sm mt-2 inline-block">+ Tambah Dompet</Link>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout: Transactions & Budget -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Transactions -->
                <div class="bg-paper border border-oat-dark rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-fraunces font-semibold text-ink">Transaksi Terbaru</h2>
                        <Link :href="route('admin.finance.transactions.index')" class="text-sm text-terracotta hover:text-terracotta/80">Lihat Semua</Link>
                    </div>
                    <div class="space-y-3">
                        <div v-for="t in transactionList" :key="t.id" class="flex items-center justify-between py-3 border-b border-oat last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center" :style="{ backgroundColor: (t.category?.color || '#ccc') + '20' }">
                                    <ArrowTrendingUpIcon v-if="t.type === 'income'" class="w-4 h-4" :style="{ color: t.category?.color || '#666' }" />
                                    <ArrowTrendingDownIcon v-else class="w-4 h-4" :style="{ color: t.category?.color || '#666' }" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-ink">{{ t.description || '-' }}</p>
                                    <p class="text-xs text-taupe">{{ t.category?.name || 'Tanpa kategori' }} &bull; {{ formatDate(t.date) }}</p>
                                </div>
                            </div>
                            <span :class="t.type === 'income' ? 'text-green-600 font-medium text-sm font-variant-numeric tabular-nums' : 'text-red-600 font-medium text-sm font-variant-numeric tabular-nums'">
                                {{ t.type === 'income' ? '+' : '-' }}{{ formatRupiah(t.amount) }}
                            </span>
                        </div>
                        <div v-if="transactionList.length === 0" class="text-center py-8">
                            <p class="text-taupe">Belum ada transaksi</p>
                        </div>
                    </div>
                </div>

                <!-- Budget Progress -->
                <div class="bg-paper border border-oat-dark rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-fraunces font-semibold text-ink">Budget Bulan Ini</h2>
                        <Link :href="route('admin.finance.budgets.index')" class="text-sm text-terracotta hover:text-terracotta/80">Kelola</Link>
                    </div>
                    <div class="space-y-4">
                        <div v-for="b in budgetList" :key="b.id" class="bg-cream rounded-xl p-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-ink">{{ b.category?.name || 'Tanpa kategori' }}</span>
                                <span class="text-xs text-taupe font-variant-numeric tabular-nums">{{ formatRupiah(b.spent) }} / {{ formatRupiah(b.amount) }}</span>
                            </div>
                            <div class="w-full bg-paper rounded-full h-2">
                                <div class="h-2 rounded-full transition-all" :class="getBudgetColor(calcBudgetPercentage(b.spent, b.amount))" :style="{ width: Math.min(calcBudgetPercentage(b.spent, b.amount), 100) + '%' }"></div>
                            </div>
                            <div class="flex justify-end mt-1">
                                <span class="text-xs font-medium" :class="calcBudgetPercentage(b.spent, b.amount) >= 100 ? 'text-red-600' : 'text-taupe'">{{ Math.round(calcBudgetPercentage(b.spent, b.amount)) }}%</span>
                            </div>
                        </div>
                        <div v-if="budgetList.length === 0" class="text-center py-8">
                            <p class="text-taupe">Belum ada budget bulan ini</p>
                            <Link :href="route('admin.finance.budgets.index')" class="text-terracotta hover:underline text-sm mt-2 inline-block">+ Tambah Budget</Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Savings Goals -->
            <div class="bg-paper border border-oat-dark rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-fraunces font-semibold text-ink">Target Tabungan</h2>
                    <Link :href="route('admin.finance.savings-goals.index')" class="text-sm text-terracotta hover:text-terracotta/80 font-medium">Kelola</Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="g in goalList" :key="g.id" class="bg-cream rounded-xl p-4 hover:bg-cream/70 transition-colors duration-200">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :style="{ backgroundColor: (g.color || '#ccc') + '20' }">
                                <FlagIcon class="w-5 h-5" :style="{ color: g.color || '#666' }" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-fraunces font-semibold text-ink truncate">{{ g.name || '-' }}</p>
                                <p v-if="g.target_date" class="text-xs text-taupe">Target: {{ formatDate(g.target_date) }}</p>
                            </div>
                        </div>
                        <div class="w-full bg-paper rounded-full h-2 mb-2">
                            <div class="h-2 bg-terracotta rounded-full transition-all" :style="{ width: calcPercentage(g.current_amount, g.target_amount) + '%' }"></div>
                        </div>
                        <div class="flex justify-between text-sm font-variant-numeric tabular-nums">
                            <span class="font-medium text-ink">{{ formatRupiah(g.current_amount) }}</span>
                            <span class="text-taupe">{{ formatRupiah(g.target_amount) }}</span>
                        </div>
                        <div class="flex justify-end mt-1">
                            <span class="text-xs font-medium text-terracotta">{{ Math.round(calcPercentage(g.current_amount, g.target_amount)) }}%</span>
                        </div>
                    </div>
                    <div v-if="goalList.length === 0" class="col-span-full text-center py-8 border border-dashed border-oat rounded-xl">
                        <p class="text-taupe">Belum ada target tabungan</p>
                        <Link :href="route('admin.finance.savings-goals.create')" class="text-terracotta hover:underline text-sm mt-2 inline-block">+ Tambah Target</Link>
                    </div>
                </div>
            </div>

            <!-- Unpaid Invoices Table -->
            <div class="bg-paper border border-oat-dark rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-fraunces font-semibold text-ink">Invoice Belum Lunas</h2>
                    <Link :href="route('admin.finance.invoices.index')" class="text-sm text-terracotta hover:text-terracotta/80">Lihat Semua</Link>
                </div>
                <div class="overflow-x-auto">
                    <table v-if="invoiceList.length > 0" class="w-full">
                        <thead>
                            <tr class="border-b border-oat">
                                <th class="text-left py-3 px-3 text-sm font-medium text-taupe">Invoice</th>
                                <th class="text-left py-3 px-3 text-sm font-medium text-taupe">Klien</th>
                                <th class="text-left py-3 px-3 text-sm font-medium text-taupe">Jatuh Tempo</th>
                                <th class="text-right py-3 px-3 text-sm font-medium text-taupe">Total</th>
                                <th class="text-center py-3 px-3 text-sm font-medium text-taupe">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inv in invoiceList" :key="inv.id" class="border-b border-oat last:border-0 hover:bg-cream/50 transition-colors">
                                <td class="py-3 px-3">
                                    <Link :href="route('admin.finance.invoices.show', inv.id)" class="text-terracotta hover:underline font-medium">{{ inv.invoice_number }}</Link>
                                </td>
                                <td class="py-3 px-3 text-ink">{{ inv.client_name }}</td>
                                <td class="py-3 px-3 text-ink">{{ formatDate(inv.due_date) }}</td>
                                <td class="py-3 px-3 text-right font-medium text-ink font-variant-numeric tabular-nums">{{ formatRupiah(inv.total) }}</td>
                                <td class="py-3 px-3 text-center">
                                    <span :class="[getStatusClass(inv.status), 'px-2 py-1 rounded-full text-xs font-medium capitalize']">{{ inv.status }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="text-center py-8 text-taupe">Tidak ada invoice outstanding</p>
                </div>
            </div>
        </div>
    </div>
</template>
