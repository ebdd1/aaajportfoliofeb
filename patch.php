<?php

$content = file_get_contents('/root/mycvebry/portfolio-febryanus/app/Http/Controllers/Admin/Finance/TransactionController.php');

$content = str_replace(
    'use Illuminate\Http\Response;',
    'use Symfony\Component\HttpFoundation\StreamedResponse;',
    $content
);

$old_export = <<<'EOT'
    public function export(Request $request): Response
    {
        $query = Transaction::with(['wallet', 'category']);

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', Carbon::parse($request->date_to));
        }

        $transactions = $query->orderBy('date', 'desc')->get();

        $csvContent = "Tanggal,Kategori,Tipe,Deskripsi,Dompet,Jumlah\n";

        foreach ($transactions as $t) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $t->date->format('Y-m-d'),
                $t->category->name,
                $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                $t->description,
                $t->wallet->name,
                number_format($t->amount, 2, ',', '.')
            );
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transaksi-' . date('Y-m-d') . '.csv"',
        ]);
    }
EOT;

$new_export = <<<'EOT'
    public function export(Request $request): StreamedResponse
    {
        $query = Transaction::with(['wallet', 'category']);

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', Carbon::parse($request->date_to));
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transaksi-' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'Kategori', 'Tipe', 'Deskripsi', 'Dompet', 'Jumlah']);

            $query->orderBy('date', 'desc')->chunk(500, function ($transactions) use ($handle) {
                foreach ($transactions as $t) {
                    fputcsv($handle, [
                        $t->date->format('Y-m-d'),
                        $t->category->name,
                        $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                        $t->description,
                        $t->wallet->name,
                        number_format((float) $t->amount, 2, ',', '.')
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
EOT;

$content = str_replace($old_export, $new_export, $content);

file_put_contents('/root/mycvebry/portfolio-febryanus/app/Http/Controllers/Admin/Finance/TransactionController.php', $content);

echo "Patched.";
