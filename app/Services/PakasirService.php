<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakasirService
{
    protected string $project;
    protected string $apiKey;
    protected string $baseUrl = 'https://app.pakasir.com/api';

    public function __construct()
    {
        $this->project = config('services.pakasir.project', env('PAKASIR_PROJECT', ''));
        $this->apiKey = config('services.pakasir.api_key', env('PAKASIR_API_KEY', ''));
    }

    public function createTransaction(string $method, string $orderId, int $amount): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/transactioncreate/{$method}", [
                'project' => $this->project,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json('payment', []);
            }

            Log::error('Pakasir transaction failed', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Pakasir API error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return [];
        }
    }

    public function getTransactionStatus(string $orderId, int $amount): ?array
    {
        try {
            $response = Http::get("{$this->baseUrl}/transactiondetail", [
                'project' => $this->project,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json('transaction');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Pakasir status check failed', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function cancelTransaction(string $orderId, int $amount): bool
    {
        try {
            $response = Http::post("{$this->baseUrl}/transactioncancel", [
                'project' => $this->project,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Pakasir cancel failed', ['message' => $e->getMessage()]);
            return false;
        }
    }

    public function isConfigured(): bool
    {
        return trim($this->project) !== '' && trim($this->apiKey) !== '';
    }

    public function simulatePayment(string $orderId, int $amount): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/paymentsimulation", [
                'project' => $this->project,
                'order_id' => $orderId,
                'amount' => $amount,
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                Log::info('Pakasir payment simulation triggered', [
                    'order_id' => $orderId,
                    'amount' => $amount,
                ]);
                return $response->json();
            }

            Log::error('Pakasir payment simulation failed', [
                'order_id' => $orderId,
                'status_code' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Pakasir simulation error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return [];
        }
    }
}
