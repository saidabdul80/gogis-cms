<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GirasService
{
    private string $baseUrl;
    private string $clientId;
    private string $secretKey;
    private array $headers;

    public function __construct()
    {
        $this->baseUrl = config('services.giras.base_url', 'https://giras-backend.staging.irs.gm.gov.ng');
        $this->clientId = config('services.giras.client_id', '');
        $this->secretKey = config('services.giras.secret_key', '');
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'x-client-id' => $this->clientId,
        ];
    }

    /**
     * Get revenue sub head ID by key
     *
     * @param string $key The revenue sub head key (default, property_tax, etc.)
     * @return int|null
     */
    public function getRevenueSubHeadId(string $key = 'default'): ?int
    {
        return config("services.giras.revenue_sub_heads.{$key}");
    }

    /**
     * Get all configured revenue sub heads
     *
     * @return array
     */
    public function getAllRevenueSubHeads(): array
    {
        return config('services.giras.revenue_sub_heads', []);
    }

    /**
     * Get default payment gateway
     *
     * @return string
     */
    public function getDefaultGateway(): string
    {
        return config('services.giras.default_gateway', 'paystack');
    }

    /**
     * Get available payment gateways
     *
     * @return array
     */
    public function getAvailableGateways(): array
    {
        return config('services.giras.gateways', ['paystack', 'remita', 'interswitch']);
    }

    /**
     * Create a single tax invoice in GIRAS
     *
     * @param array $invoiceData
     * @return array
     * @throws Exception
     */
    public function createTaxInvoice(array $invoiceData): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->post("{$this->baseUrl}/tax_invoices", $invoiceData);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('GIRAS Invoice Creation Failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'request' => $invoiceData
            ]);

            throw new Exception($response->json()['message'] ?? 'Failed to create invoice in GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS API Error', [
                'message' => $e->getMessage(),
                'request' => $invoiceData
            ]);
            throw $e;
        }
    }

    /**
     * Create multiple tax invoices in GIRAS
     *
     * @param array $entries Array of invoice entries
     * @param array $taxpayerData Taxpayer data (first_name, last_name, phone_number, email)
     * @param string $gateway Payment gateway (paystack, remita, interswitch)
     * @return array
     * @throws Exception
     */
    public function createMultiTaxInvoices(array $entries, array $taxpayerData, string $gateway = 'paystack'): array
    {
        try {
            $payload = [
                'entries' => $entries,
                'first_name' => $taxpayerData['first_name'],
                'last_name' => $taxpayerData['last_name'],
                'phone_number' => $taxpayerData['phone_number'],
                'email' => $taxpayerData['email'] ?? null,
                'gateway' => $gateway
            ];

            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->post("{$this->baseUrl}/multi_tax_invoices", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data;
            }

            $errorData = $response->json();
            Log::error('GIRAS Multi Invoice Creation Failed', [
                'status' => $response->status(),
                'response' => $errorData,
                'request' => $payload
            ]);

            throw new Exception($errorData['message'] ?? 'Failed to create invoices in GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Multi Invoice API Error', [
                'message' => $e->getMessage(),
                'request' => $entries
            ]);
            throw new Exception($e->getMessage(),400);
        }
    }

    /**
     * Get invoice details from GIRAS
     *
     * @param int $invoiceId
     * @return array
     * @throws Exception
     */
    public function getInvoiceDetails(int $invoiceId): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/tax_invoices/{$invoiceId}");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to fetch invoice details from GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Get Invoice Error', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoiceId
            ]);
            throw $e;
        }
    }

    /**
     * Extract required variables for a revenue sub head
     *
     * @param int $revenueSubHeadId
     * @param int|null $wardId
     * @param string|null $revenueTypeCategory
     * @return array
     * @throws Exception
     */
    public function extractVariables(int $revenueSubHeadId, ?int $wardId = null, ?string $revenueTypeCategory = null): array
    {
        try {
            $params = ['revenue_sub_head_id' => $revenueSubHeadId];

            if ($wardId) {
                $params['ward_id'] = $wardId;
            }

            if ($revenueTypeCategory) {
                $params['revenue_type_category'] = $revenueTypeCategory;
            }

            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/tax_invoices/extract/variables", $params);

            if ($response->successful()) {
                $data = $response->json();

                // Response format: [variables_object, template_string]
                // Example: [{"property_value": 0, "rate": 0}, "{property_value} * {rate} / 100"]
                if (is_array($data) && count($data) >= 2) {
                    return [
                        'variables' => $data[0] ?? [],
                        'template' => $data[1] ?? '',
                    ];
                }

                return [
                    'variables' => [],
                    'template' => is_string($data) ? $data : '',
                ];
            }

            throw new Exception('Failed to extract variables from GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Extract Variables Error', [
                'message' => $e->getMessage(),
                'revenue_sub_head_id' => $revenueSubHeadId
            ]);
            throw $e;
        }
    }

    /**
     * Get revenue sources
     *
     * @return array
     * @throws Exception
     */
    public function getRevenueSources(): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/revenue_sources");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to fetch revenue sources from GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Get Revenue Sources Error', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get revenue heads
     *
     * @return array
     * @throws Exception
     */
    public function getRevenueHeads(): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/revenue_heads");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to fetch revenue heads from GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Get Revenue Heads Error', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get revenue sub heads
     *
     * @return array
     * @throws Exception
     */
    public function getRevenueSubHeads(): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/revenue_sub_heads");

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception('Failed to fetch revenue sub heads from GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Get Revenue Sub Heads Error', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get a specific revenue sub head with invoice types
     *
     * @param int $revenueSubHeadId
     * @return array
     * @throws Exception
     */
    public function getRevenueSubHead(int $revenueSubHeadId): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/revenue_sub_heads/{$revenueSubHeadId}");

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'] ?? $data;
            }

            throw new Exception('Failed to fetch revenue sub head from GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Get Revenue Sub Head Error', [
                'message' => $e->getMessage(),
                'revenue_sub_head_id' => $revenueSubHeadId
            ]);
            throw $e;
        }
    }

    /**
     * Initiate payment for invoices
     *
     * @param array $invoiceIds Array of GIRAS tax invoice IDs
     * @param string $gateway Payment gateway (paystack, remita, interswitch, etc.)
     * @param string|null $reference Optional custom reference
     * @param string|null $callbackUrl Optional callback URL for payment completion
     * @return array Payment initiation response with 'link', 'reference', and 'gateway'
     * @throws Exception
     */
    public function initiatePayment(array $invoiceIds, string $gateway = 'paystack', ?string $reference = null, ?string $callbackUrl = null): array
    {
        try {
            $payload = [
                'invoice_id' => $invoiceIds,
                'gateway' => $gateway,
            ];

            if ($reference) {
                $payload['reference'] = $reference;
            }

            if ($callbackUrl) {
                $payload['callback_url'] = $callbackUrl;
            }

            Log::info('GIRAS Payment Initiation Request', $payload);

            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->post("{$this->baseUrl}/pay", $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('GIRAS Payment Initiation Response', $data);

                // Return structured response with gateway info
                return [
                    'link' => $data['link'] ?? null,
                    'reference' => $data['reference'] ?? null,
                    'gateway' => $gateway,
                ];
            }

            $errorData = $response->json();
            Log::error('GIRAS Payment Initiation Failed', [
                'status' => $response->status(),
                'response' => $errorData,
                'request' => $payload
            ]);

            throw new Exception($errorData['message'] ?? 'Failed to initiate payment in GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Payment Initiation Error', [
                'message' => $e->getMessage(),
                'invoice_ids' => $invoiceIds
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment status with GIRAS
     *
     * @param string $reference Payment reference
     * @return array Payment verification response
     * @throws Exception
     */
    public function verifyPayment(string $reference): array
    {
        try {
            Log::info('GIRAS Payment Verification Request', ['reference' => $reference]);

            $response = Http::withHeaders($this->headers)
                ->timeout(30)
                ->get("{$this->baseUrl}/payment/verify/{$reference}");

            if ($response->successful()) {
                $data = $response->json();
                Log::info('GIRAS Payment Verification Response', $data);

                return $data;
            }

            $errorData = $response->json();
            Log::error('GIRAS Payment Verification Failed', [
                'status' => $response->status(),
                'response' => $errorData,
                'reference' => $reference
            ]);

            throw new Exception($errorData['message'] ?? 'Failed to verify payment with GIRAS');
        } catch (Exception $e) {
            Log::error('GIRAS Payment Verification Error', [
                'message' => $e->getMessage(),
                'reference' => $reference
            ]);
            throw $e;
        }
    }

    /**
     * Build taxpayer data for GIRAS from GOGIS customer
     *
     * @param object $customer
     * @return array
     */
    public function buildTaxpayerData($customer): array
    {
        $customerType = get_class($customer);

        // Map GOGIS customer to GIRAS taxpayer format
        if ($customerType === 'App\\Models\\IndividualCustomer') {
            return [
                'tax_payer_id' => $customer->id,
                'tax_payer_type' => 'App\\Models\\IndividualTaxPayer', // GIRAS expects this format
            ];
        } elseif ($customerType === 'App\\Models\\CorporateCustomer') {
            return [
                'tax_payer_id' => $customer->id,
                'tax_payer_type' => 'App\\Models\\CorporateTaxPayer', // GIRAS expects this format
            ];
        }

        throw new Exception('Invalid customer type');
    }
}

