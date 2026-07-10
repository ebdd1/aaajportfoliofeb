<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Pending</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #FAF8F1; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #C96442; color: #FAF8F1; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 32px 24px; }
        .order-box { background: #F5F1E8; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .order-box h2 { margin: 0 0 16px; color: #3D3929; font-size: 18px; }
        .order-row { display: flex; justify-content: space-between; margin: 8px 0; color: #6B6456; }
        .order-row strong { color: #3D3929; }
        .amount { font-size: 28px; font-weight: bold; color: #C96442; text-align: center; margin: 20px 0; }
        .cta { text-align: center; margin: 24px 0; }
        .cta a { background: #C96442; color: #FAF8F1; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; }
        .cta a:hover { background: #B5532F; }
        .warning { background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 12px 16px; margin: 16px 0; border-radius: 0 8px 8px 0; color: #92400E; font-size: 14px; }
        .footer { background: #F5F1E8; padding: 20px 24px; text-align: center; font-size: 12px; color: #6B6456; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reminder Pembayaran</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $order->user->name }}</strong>,</p>
            <p>Order kamu dengan detail berikut belum selesai dibayar:</p>

            <div class="order-box">
                <h2>Detail Order</h2>
                <div class="order-row">
                    <span>Order ID</span>
                    <strong>{{ $order->order_id }}</strong>
                </div>
                <div class="order-row">
                    <span>Metode Pembayaran</span>
                    <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'QRIS')) }}</strong>
                </div>
                @if($order->expired_at)
                <div class="order-row">
                    <span>Batas Waktu</span>
                    <strong>{{ $order->expired_at->format('d M Y, H:i') }} WIB</strong>
                </div>
                @endif
            </div>

            <div class="amount">
                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
            </div>

            <div class="cta">
                <a href="{{ route('payment.show', $order->order_id) }}">Lanjutkan Pembayaran</a>
            </div>

            <div class="warning">
                <strong>⚠️ Penting:</strong> Pastikan menyelesaikan pembayaran sebelum batas waktu habis. Setelah expired, kamu perlu membuat order baru.
            </div>

            <p>Jika kamu sudah melakukan pembayaran, abaikan email ini. Pembayaran biasanya diproses dalam 1-5 menit.</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem. Jangan reply email ini.</p>
            <p>&copy; {{ date('Y') }} Portfolio Febryanus</p>
        </div>
    </div>
</body>
</html>
