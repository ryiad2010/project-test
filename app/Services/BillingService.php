<?php

namespace App\Services;

use App\Models\BillingRecord;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillingService
{
    protected string $paymentGatewayUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->paymentGatewayUrl = config('billing.payment_gateway_url');
        $this->apiKey = config('billing.api_key');
    }

    public function createInvoice(User $user, array $lineItems, ?string $dueDate = null): BillingRecord
    {
        $amount = collect($lineItems)->sum(fn($item) => $item['quantity'] * $item['price']);

        $billing = BillingRecord::create([
            'user_id' => $user->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $amount,
            'line_items' => $lineItems,
            'due_date' => $dueDate ? now()->parse($dueDate) : now()->addDays(30),
        ]);

        return $billing;
    }

    public function processPayment(BillingRecord $billing, array $paymentData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->paymentGatewayUrl . '/charges', [
                'amount' => $billing->amount * 100, // Convert to cents
                'currency' => $billing->currency,
                'source' => $paymentData['token'],
                'description' => 'Invoice: ' . $billing->invoice_number,
                'metadata' => [
                    'invoice_id' => $billing->id,
                    'user_id' => $billing->user_id,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $billing->markAsPaid($data['id']);

                return [
                    'success' => true,
                    'transaction_id' => $data['id'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Payment failed',
            ];
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'billing_id' => $billing->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment processing failed. Please try again.',
            ];
        }
    }

    public function refundPayment(BillingRecord $billing, ?float $amount = null): array
    {
        try {
            $refundAmount = $amount ? $amount * 100 : $billing->amount * 100;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->paymentGatewayUrl . '/refunds', [
                'charge' => $billing->transaction_id,
                'amount' => $refundAmount,
            ]);

            if ($response->successful()) {
                $billing->update(['status' => 'refunded']);

                return [
                    'success' => true,
                    'refund_id' => $response->json()['id'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Refund failed',
            ];
        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'billing_id' => $billing->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Refund processing failed. Please try again.',
            ];
        }
    }

    public function generateInvoiceNumber(): string
    {
        $lastInvoice = BillingRecord::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;

        return 'INV-' . date('Y') . '-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
