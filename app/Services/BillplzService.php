<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BillplzService
{
    protected Client $client;
    protected string $apiKey;
    protected string $collectionId;
    protected string $xSignatureKey;
    protected string $baseUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->apiKey = config('services.billplz.api_key');
        $this->collectionId = config('services.billplz.collection_id');
        $this->xSignatureKey = config('services.billplz.x_signature_key');
        $this->baseUrl = config('services.billplz.url');
        $this->sandbox = config('services.billplz.sandbox');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'auth' => [$this->apiKey, ''], // API key as username, no password
            'headers' => [
                'Accept' => 'application/json',
            ],
            'verify' => !$this->sandbox, // Disable SSL verification in sandbox
        ]);
    }

    /**
     * Create a new bill for payroll payment
     *
     * @param array $data Bill data
     * @return array|null
     */
    public function createBill(array $data): ?array
    {
        try {
            $response = $this->client->post('bills', [
                'form_params' => [
                    'collection_id' => $this->collectionId,
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'amount' => $this->convertToSen($data['amount']), // Convert RM to sen (cents)
                    'callback_url' => $data['callback_url'],
                    'redirect_url' => $data['redirect_url'] ?? $data['callback_url'],
                    'description' => $data['description'] ?? 'Payroll Payment',
                    'reference_1_label' => $data['reference_1_label'] ?? 'Payroll ID',
                    'reference_1' => $data['reference_1'] ?? '',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('Billplz bill created successfully', [
                'bill_id' => $result['id'] ?? null,
                'url' => $result['url'] ?? null,
            ]);

            return $result;
        } catch (GuzzleException $e) {
            Log::error('Billplz bill creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return null;
        }
    }

    /**
     * Get bill details
     *
     * @param string $billId
     * @return array|null
     */
    public function getBill(string $billId): ?array
    {
        try {
            $response = $this->client->get("bills/{$billId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Billplz get bill failed', [
                'bill_id' => $billId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Validate webhook signature
     *
     * @param string $billplzId
     * @param string $xSignature
     * @return bool
     */
    public function validateSignature(string $billplzId, string $xSignature): bool
    {
        $computedSignature = hash_hmac('sha256', $this->xSignatureKey . '|' . $billplzId, $this->apiKey);

        return hash_equals($computedSignature, $xSignature);
    }

    /**
     * Convert RM amount to sen (cents)
     * RM 1,721.25 = 172125 sen
     *
     * @param float $amount
     * @return int
     */
    protected function convertToSen(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Convert sen to RM amount
     *
     * @param int $sen
     * @return float
     */
    public function convertToRinggit(int $sen): float
    {
        return $sen / 100;
    }

    /**
     * Get payment URL with auto-submit for direct gateway
     *
     * @param string $billUrl
     * @return string
     */
    public function getDirectPaymentUrl(string $billUrl): string
    {
        return $billUrl . '?auto_submit=true';
    }

    /**
     * Create a collection (one-time setup)
     *
     * @param string $title
     * @return array|null
     */
    public function createCollection(string $title): ?array
    {
        try {
            $response = $this->client->post('collections', [
                'form_params' => [
                    'title' => $title,
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error('Billplz collection creation failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Check if sandbox mode is enabled
     *
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
