<?php

namespace Database\Seeders;

use App\Models\Finance\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        $wallets = [
            [
                'name' => 'Cash',
                'type' => 'cash',
                'balance' => 0,
                'color' => '#6b7280',
                'icon' => 'WalletIcon',
            ],
            [
                'name' => 'BCA',
                'type' => 'bank',
                'balance' => 0,
                'color' => '#0052cc',
                'icon' => 'BuildingLibraryIcon',
            ],
            [
                'name' => 'DANA',
                'type' => 'ewallet',
                'balance' => 0,
                'color' => '#118eed',
                'icon' => 'DevicePhoneMobileIcon',
            ],
        ];

        foreach ($wallets as $wallet) {
            Wallet::updateOrCreate(
                ['name' => $wallet['name']],
                $wallet
            );
        }
    }
}
