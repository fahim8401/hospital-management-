<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    private string $bkashBaseUrl;
    private string $bkashAppKey;
    private string $bkashAppSecret;
    private string $bkashUsername;
    private string $bkashPassword;
    private string $nagadBaseUrl;
    private string $nagadMerchantId;

    public function __construct()
    {
        $this->bkashBaseUrl   = config('services.bkash.base_url', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta');
        $this->bkashAppKey    = config('services.bkash.app_key', '');
        $this->bkashAppSecret = config('services.bkash.app_secret', '');
        $this->bkashUsername  = config('services.bkash.username', '');
        $this->bkashPassword  = config('services.bkash.password', '');

        $this->nagadBaseUrl   = config('services.nagad.base_url', 'https://api.mynagad.com');
        $this->nagadMerchantId = config('services.nagad.merchant_id', '');
    }

    /**
     * Initiate a bKash tokenized payment for an invoice.
     *
     * @return array{status: string, paymentID?: string, bkashURL?: string, error?: string}
     */
    public function initiateBkashPayment(int $invoiceId): array
    {
        $invoice = Invoice::with('patient')->findOrFail($invoiceId);

        try {
            // Step 1: Grant token
            $tokenResponse = Http::timeout(15)
                ->withHeaders([
                    'username'  => $this->bkashUsername,
                    'password'  => $this->bkashPassword,
                ])
                ->post("{$this->bkashBaseUrl}/tokenized/checkout/token/grant", [
                    'app_key'    => $this->bkashAppKey,
                    'app_secret' => $this->bkashAppSecret,
                ]);

            if (! $tokenResponse->successful() || empty($tokenResponse->json('id_token'))) {
                Log::error('bKash token grant failed', ['response' => $tokenResponse->body()]);
                return ['status' => 'error', 'error' => 'Failed to obtain bKash token.'];
            }

            $idToken = $tokenResponse->json('id_token');

            // Step 2: Create payment
            $createResponse = Http::timeout(15)
                ->withHeaders([
                    'authorization' => $idToken,
                    'x-app-key'     => $this->bkashAppKey,
                ])
                ->post("{$this->bkashBaseUrl}/tokenized/checkout/create", [
                    'mode'                  => '0011',
                    'payerReference'        => (string) $invoice->patient_id,
                    'callbackURL'           => route('payments.bkash.callback'),
                    'amount'                => number_format((float) $invoice->grand_total, 2, '.', ''),
                    'currency'              => 'BDT',
                    'intent'                => 'sale',
                    'merchantInvoiceNumber' => $invoice->invoice_number,
                ]);

            if (! $createResponse->successful() || $createResponse->json('statusCode') !== '0000') {
                Log::error('bKash create payment failed', ['response' => $createResponse->body()]);
                return ['status' => 'error', 'error' => $createResponse->json('statusMessage', 'Payment creation failed.')];
            }

            return [
                'status'    => 'success',
                'paymentID' => $createResponse->json('paymentID'),
                'bkashURL'  => $createResponse->json('bkashURL'),
            ];
        } catch (ConnectionException $e) {
            Log::error('bKash API connection timeout', ['invoice_id' => $invoiceId, 'error' => $e->getMessage()]);
            return ['status' => 'error', 'error' => 'bKash gateway is currently unavailable. Please try again.'];
        }
    }

    /**
     * Verify a bKash payment using the transaction ID.
     *
     * @return array{status: string, trxID?: string, amount?: string, error?: string}
     */
    public function verifyBkashPayment(string $trxId): array
    {
        try {
            // Re-grant token for verification
            $tokenResponse = Http::timeout(15)
                ->withHeaders([
                    'username' => $this->bkashUsername,
                    'password' => $this->bkashPassword,
                ])
                ->post("{$this->bkashBaseUrl}/tokenized/checkout/token/grant", [
                    'app_key'    => $this->bkashAppKey,
                    'app_secret' => $this->bkashAppSecret,
                ]);

            if (! $tokenResponse->successful() || empty($tokenResponse->json('id_token'))) {
                return ['status' => 'error', 'error' => 'Failed to obtain bKash token for verification.'];
            }

            $idToken = $tokenResponse->json('id_token');

            $executeResponse = Http::timeout(15)
                ->withHeaders([
                    'authorization' => $idToken,
                    'x-app-key'     => $this->bkashAppKey,
                ])
                ->post("{$this->bkashBaseUrl}/tokenized/checkout/execute", [
                    'paymentID' => $trxId,
                ]);

            if (! $executeResponse->successful() || $executeResponse->json('statusCode') !== '0000') {
                Log::error('bKash payment verification failed', ['response' => $executeResponse->body()]);
                return ['status' => 'error', 'error' => $executeResponse->json('statusMessage', 'Verification failed.')];
            }

            return [
                'status' => 'success',
                'trxID'  => $executeResponse->json('trxID'),
                'amount' => $executeResponse->json('amount'),
            ];
        } catch (ConnectionException $e) {
            Log::error('bKash verification connection timeout', ['trxId' => $trxId, 'error' => $e->getMessage()]);
            return ['status' => 'error', 'error' => 'bKash gateway is currently unavailable. Please try again.'];
        }
    }

    /**
     * Initiate a Nagad payment for an invoice.
     *
     * @return array{status: string, callBackUrl?: string, error?: string}
     */
    public function initiateNagadPayment(int $invoiceId): array
    {
        $invoice = Invoice::with('patient')->findOrFail($invoiceId);

        try {
            $orderId = $invoice->invoice_number;

            $initResponse = Http::timeout(15)
                ->withHeaders([
                    'X-KM-Api-Version' => 'v-0.2.0',
                    'X-KM-IP-V4'       => request()->ip(),
                    'X-KM-Client-Type' => 'PC_WEB',
                ])
                ->post("{$this->nagadBaseUrl}/api/dfs/check-out/initialize/{$this->nagadMerchantId}/{$orderId}", [
                    'dateTime'        => now()->format('Ymdhis'),
                    'amount'          => number_format((float) $invoice->grand_total, 2, '.', ''),
                    'callbackURL'     => route('payments.nagad.callback'),
                    'merchantOrderId' => $orderId,
                    'additionalMerchantInfo' => [
                        'invoice_id' => $invoice->id,
                    ],
                ]);

            if (! $initResponse->successful() || $initResponse->json('status') !== 'Success') {
                Log::error('Nagad initiate payment failed', ['response' => $initResponse->body()]);
                return ['status' => 'error', 'error' => $initResponse->json('message', 'Nagad payment initiation failed.')];
            }

            return [
                'status'      => 'success',
                'callBackUrl' => $initResponse->json('callBackUrl'),
            ];
        } catch (ConnectionException $e) {
            Log::error('Nagad API connection timeout', ['invoice_id' => $invoiceId, 'error' => $e->getMessage()]);
            return ['status' => 'error', 'error' => 'Nagad gateway is currently unavailable. Please try again.'];
        }
    }
}
