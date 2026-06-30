<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Expire pending orders that have passed their expiration time';

    public function handle(): int
    {
        $expiredOrders = Order::where('status', Order::STATUS_PENDING)
            ->where('expired_at', '<', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiredOrders as $order) {
            $order->markAsExpired();
            $count++;

            Log::info('Order expired automatically', [
                'order_id' => $order->order_id,
                'expired_at' => $order->expired_at,
            ]);
        }

        $this->info("Successfully expired {$count} orders.");

        return Command::SUCCESS;
    }
}
