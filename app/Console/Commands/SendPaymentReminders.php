<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'orders:remind
                            {--dry-run : Show orders that would receive reminders without sending}';

    protected $description = 'Send payment reminders for pending orders';

    public function handle(): int
    {
        // Get pending orders that:
        // 1. Created more than 1 hour ago
        // 2. Not expired yet
        // 3. Never got a reminder (we could track this in order table)
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<', now()->subHours(1))
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', now());
            })
            ->with('user')
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('No pending orders need reminders.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingOrders->count()} orders needing reminders.");

        if ($this->option('dry-run')) {
            $this->table(
                ['Order ID', 'User', 'Amount', 'Created', 'Expires'],
                $pendingOrders->map(fn ($order) => [
                    $order->order_id,
                    $order->user->name,
                    'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                    $order->created_at->diffForHumans(),
                    $order->expired_at?->diffForHumans() ?? 'N/A',
                ])
            );
            return Command::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($pendingOrders as $order) {
            try {
                Mail::to($order->user)->queue(new PaymentReminderMail($order));

                Log::info('Payment reminder sent', [
                    'order_id' => $order->order_id,
                    'user_id' => $order->user_id,
                ]);

                $sent++;
                $this->line("✓ Reminder sent for {$order->order_id}");
            } catch (\Exception $e) {
                $failed++;
                Log::error('Failed to send payment reminder', [
                    'order_id' => $order->order_id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("✗ Failed for {$order->order_id}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Sent: {$sent} | Failed: {$failed}");

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
