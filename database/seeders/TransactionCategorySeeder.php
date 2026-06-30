<?php

namespace Database\Seeders;

use App\Models\Finance\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Income categories
            [
                'name' => 'Gaji',
                'type' => 'income',
                'icon' => 'BriefcaseIcon',
                'color' => '#22c55e',
            ],
            [
                'name' => 'Freelance',
                'type' => 'income',
                'icon' => 'ComputerDesktopIcon',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Bonus',
                'type' => 'income',
                'icon' => 'GiftIcon',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Investasi',
                'type' => 'income',
                'icon' => 'ChartBarIcon',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Lainnya',
                'type' => 'income',
                'icon' => 'EllipsisHorizontalIcon',
                'color' => '#6b7280',
            ],

            // Expense categories
            [
                'name' => 'Makan',
                'type' => 'expense',
                'icon' => 'ShoppingCartIcon',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Transport',
                'type' => 'expense',
                'icon' => 'TruckIcon',
                'color' => '#f97316',
            ],
            [
                'name' => 'Pulsa/Internet',
                'type' => 'expense',
                'icon' => 'WifiIcon',
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Belanja',
                'type' => 'expense',
                'icon' => 'ShoppingBagIcon',
                'color' => '#ec4899',
            ],
            [
                'name' => 'Hiburan',
                'type' => 'expense',
                'icon' => 'MusicalNoteIcon',
                'color' => '#a855f7',
            ],
            [
                'name' => 'Kesehatan',
                'type' => 'expense',
                'icon' => 'HeartIcon',
                'color' => '#10b981',
            ],
            [
                'name' => 'Pendidikan',
                'type' => 'expense',
                'icon' => 'AcademicCapIcon',
                'color' => '#6366f1',
            ],
            [
                'name' => 'Tabungan',
                'type' => 'expense',
                'icon' => 'BanknotesIcon',
                'color' => '#14b8a6',
            ],
            [
                'name' => 'Lainnya',
                'type' => 'expense',
                'icon' => 'EllipsisHorizontalIcon',
                'color' => '#6b7280',
            ],
        ];

        foreach ($categories as $category) {
            TransactionCategory::updateOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                array_merge($category, ['is_default' => true])
            );
        }
    }
}
