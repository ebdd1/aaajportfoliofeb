<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pesanan Baru #' . $this->order->getShortOrderId())
            ->line('Ada pesanan baru yang masuk!')
            ->line('Customer: ' . $this->order->user->name)
            ->line('Jumlah: Rp ' . number_format($this->order->total_amount, 0, ',', '.'))
            ->action('Lihat Pesanan', url('/admin/orders/' . $this->order->id))
            ->line('Segera proses pesanan ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_id,
            'customer_name' => $this->order->user->name,
            'customer_email' => $this->order->user->email,
            'amount' => $this->order->total_amount,
            'type' => 'new_order',
        ];
    }

    public static function notifyAdmin(Order $order): void
    {
        // Notify all admins
        $admins = \App\Models\User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            $admin->notify(new self($order));
        }

        // Also notify via email if admin email is set
        $adminEmail = config('mail.admin_email') ?: config('mail.from.address');
        if ($adminEmail) {
            FacadesNotification::route('mail', $adminEmail)
                ->notify(new self($order));
        }
    }
}
