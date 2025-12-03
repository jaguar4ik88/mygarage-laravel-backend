<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppleReceiptValidator
{
    private const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';
    private const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';

    /**
     * Validate Apple receipt according to Apple's guidelines:
     * 1. Always validate against production first
     * 2. If error code 21007 (Sandbox receipt used in production), validate against sandbox
     *
     * This method follows Apple's recommended approach:
     * - Production server should first check production App Store
     * - If status 21007 is returned, retry the request in the sandbox environment
     *
     * @param string $receiptData Base64 encoded receipt data
     * @param string|null $sharedSecret App Store Shared Secret (optional for auto-renewable subscriptions)
     * @return array|null Validation result or null on failure
     */
    public function validate(string $receiptData, ?string $sharedSecret = null): ?array
    {
        $sharedSecret = $sharedSecret ?? config('services.apple.shared_secret');

        // Step 1: Always try production first (as per Apple guidelines)
        Log::info('Apple receipt validation: Starting validation against production environment');
        $productionResult = $this->validateAgainstProduction($receiptData, $sharedSecret);

        // Check if we got a response
        if ($productionResult === null) {
            Log::error('Apple receipt validation: Production validation request failed');
            return null;
        }

        $productionStatus = $productionResult['status'] ?? null;

        // Step 2: If we get error code 21007, it means sandbox receipt was used in production
        // In this case, validate against sandbox (as per Apple guidelines)
        if ($productionStatus === 21007) {
            Log::info('Apple receipt validation: Received status 21007 (Sandbox receipt used in production), retrying in sandbox environment');
            $sandboxResult = $this->validateAgainstSandbox($receiptData, $sharedSecret);

            if ($sandboxResult === null) {
                Log::error('Apple receipt validation: Sandbox validation request failed');
                return null;
            }

            // Log the result
            $sandboxStatus = $sandboxResult['status'] ?? null;
            $sandboxEnvironment = $sandboxResult['environment'] ?? 'unknown';
            
            Log::info('Apple receipt validation: Sandbox validation completed', [
                'status' => $sandboxStatus,
                'environment' => $sandboxEnvironment,
            ]);

            // Return sandbox result (should have status 0 if valid)
            return $sandboxResult;
        }

        // Return production result (success or other error)
        $productionEnvironment = $productionResult['environment'] ?? 'unknown';
        Log::info('Apple receipt validation: Production validation completed', [
            'status' => $productionStatus,
            'environment' => $productionEnvironment,
        ]);

        return $productionResult;
    }

    /**
     * Validate receipt against production App Store
     */
    private function validateAgainstProduction(string $receiptData, ?string $sharedSecret): ?array
    {
        return $this->sendValidationRequest(self::PRODUCTION_URL, $receiptData, $sharedSecret);
    }

    /**
     * Validate receipt against sandbox App Store
     */
    private function validateAgainstSandbox(string $receiptData, ?string $sharedSecret): ?array
    {
        return $this->sendValidationRequest(self::SANDBOX_URL, $receiptData, $sharedSecret);
    }

    /**
     * Send validation request to Apple App Store
     */
    private function sendValidationRequest(string $url, string $receiptData, ?string $sharedSecret): ?array
    {
        try {
            $payload = [
                'receipt-data' => $receiptData,
            ];

            if ($sharedSecret) {
                $payload['password'] = $sharedSecret;
            }

            Log::info('Apple receipt validation request', [
                'url' => $url,
                'has_receipt_data' => !empty($receiptData),
                'has_shared_secret' => !empty($sharedSecret),
            ]);

            $response = Http::timeout(30)->post($url, $payload);

            if (!$response->successful()) {
                Log::error('Apple receipt validation HTTP error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $result = $response->json();

            Log::info('Apple receipt validation response', [
                'status' => $result['status'] ?? 'unknown',
                'environment' => $result['environment'] ?? 'unknown',
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Apple receipt validation exception', [
                'message' => $e->getMessage(),
                'url' => $url,
            ]);
            return null;
        }
    }

    /**
     * Extract subscription information from validated receipt
     * 
     * This method should only be called with valid receipt (status 0).
     * Status 21007 should be handled by validate() method before reaching here.
     */
    public function extractSubscriptionInfo(array $validationResult, string $productId): ?array
    {
        // Check if receipt is valid (only status 0 is valid)
        $status = $validationResult['status'] ?? null;
        if ($status !== 0) {
            Log::warning('Apple receipt validation: Cannot extract subscription info from invalid receipt', [
                'status' => $status,
                'environment' => $validationResult['environment'] ?? 'unknown',
            ]);
            return null;
        }

        // Get latest_receipt_info (for auto-renewable subscriptions)
        $receiptInfo = $validationResult['latest_receipt_info'] ?? [];
        if (empty($receiptInfo)) {
            // Fallback to receipt array if latest_receipt_info is not available
            $receiptInfo = $validationResult['receipt']['in_app'] ?? [];
        }

        // Find the subscription matching our product ID
        foreach ($receiptInfo as $transaction) {
            if (isset($transaction['product_id']) && $transaction['product_id'] === $productId) {
                return [
                    'transaction_id' => $transaction['transaction_id'] ?? null,
                    'original_transaction_id' => $transaction['original_transaction_id'] ?? null,
                    'product_id' => $transaction['product_id'] ?? null,
                    'purchase_date_ms' => $transaction['purchase_date_ms'] ?? null,
                    'expires_date_ms' => $transaction['expires_date_ms'] ?? null,
                    'is_trial_period' => $transaction['is_trial_period'] ?? false,
                    'is_in_intro_offer_period' => $transaction['is_in_intro_offer_period'] ?? false,
                    'cancellation_date_ms' => $transaction['cancellation_date_ms'] ?? null,
                ];
            }
        }

        Log::warning('Product ID not found in receipt', ['product_id' => $productId]);
        return null;
    }

    /**
     * Check if receipt is valid
     * 
     * Only status 0 means valid receipt.
     * Status 21007 means sandbox receipt was sent to production endpoint,
     * which should trigger sandbox validation in validate() method.
     * 
     * @param array $validationResult Validation result from Apple
     * @return bool True if receipt is valid (status 0)
     */
    public function isValid(array $validationResult): bool
    {
        $status = $validationResult['status'] ?? null;
        
        // Only status 0 means valid receipt
        // Status 21007 should not reach here - it should be handled in validate() method
        // by retrying in sandbox environment
        if ($status === 0) {
            return true;
        }
        
        // Log non-zero statuses for debugging
        Log::warning('Apple receipt validation: Invalid receipt status', [
            'status' => $status,
            'environment' => $validationResult['environment'] ?? 'unknown',
        ]);
        
        return false;
    }
}

