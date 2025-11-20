<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\IndividualCustomer;
use App\Models\CorporateCustomer;
use App\Services\GirasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Exception;

class InvoiceController extends Controller
{
    protected $girasService;

    public function __construct(GirasService $girasService)
    {
        $this->girasService = $girasService;
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $paymentStatus = $request->input('payment_status');
        $propertyType = $request->input('property_type');

        $invoices = Invoice::with(['customer', 'property.propertyType', 'issuer'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%");
                });
            })
            ->when($paymentStatus, function ($query, $status) {
                $query->where('payment_status', $status);
            })
            ->when($propertyType, function ($query, $type) {
                $query->whereHas('property', function ($q) use ($type) {
                    $q->where('property_type_id', $type);
                });
            })
            ->latest()
            ->paginate(15)
            ->through(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer' => [
                        'id' => $invoice->customer->id,
                        'name' => $invoice->customer_type === 'App\\Models\\IndividualCustomer'
                            ? $invoice->customer->first_name . ' ' . $invoice->customer->last_name
                            : $invoice->customer->company_name,
                        'type' => $invoice->customer_type === 'App\\Models\\IndividualCustomer' ? 'Individual' : 'Corporate',
                    ],
                    'property' => $invoice->property ? [
                        'id' => $invoice->property->id,
                        'number_type' => $invoice->property->number_type,
                        'number_value' => $invoice->property->number_value,
                        'address' => $invoice->property->address,
                        'property_type' => $invoice->property->propertyType ? $invoice->property->propertyType->name : null,
                    ] : null,
                    'amount' => $invoice->amount,
                    'paid_amount' => $invoice->paid_amount,
                    'payment_status' => $invoice->payment_status,
                    'issue_date' => $invoice->issue_date->format('Y-m-d'),
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                    'is_overdue' => $invoice->isOverdue(),
                    'giras_synced' => $invoice->isSyncedWithGiras(),
                    'has_payment_url' => $invoice->hasPaymentUrl(),
                ];
            });

        $propertyTypes = PropertyType::all(['id', 'name']);

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'propertyTypes' => $propertyTypes,
            'filters' => [
                'search' => $search,
                'payment_status' => $paymentStatus,
                'property_type' => $propertyType,
            ],
        ]);
    }

    /**
     * Show the form for creating a new invoice
     */
    public function create(Request $request)
    {
        $propertyId = $request->input('property_id');
        $property = null;

        if ($propertyId) {
            $property = Property::with(['customer', 'propertyType'])->findOrFail($propertyId);
        }

        $properties = Property::with(['customer', 'propertyType'])
            ->get()
            ->map(function ($prop) {
                return [
                    'id' => $prop->id,
                    'number_type' => $prop->number_type,
                    'number_value' => $prop->number_value,
                    'address' => $prop->address,
                    'amount' => $prop->amount,
                    'payment_frequency' => $prop->payment_frequency,
                    'property_type' => $prop->propertyType ? $prop->propertyType->name : null,
                    'customer' => [
                        'id' => $prop->customer->id,
                        'name' => $prop->customer_type === 'App\\Models\\IndividualCustomer'
                            ? $prop->customer->first_name . ' ' . $prop->customer->last_name
                            : $prop->customer->company_name,
                        'type' => $prop->customer_type,
                    ],
                ];
            });

        // Get GIRAS revenue sub head configuration
        $revenueSubHeadId = config('services.giras.revenue_sub_heads.property_tax');

        $girasConfig = null;

        try {
            // Extract variables from GIRAS
            $variablesData = $this->girasService->extractVariables($revenueSubHeadId);

            if (!empty($variablesData['variables'])) {
                // Set first variable to property amount if property is selected
                $variables = $variablesData['variables'];
                if ($property) {
                    $firstKey = array_key_first($variables);
                    if ($firstKey) {
                        $variables[$firstKey] = $property->amount;
                    }
                }

                $girasConfig = [
                    'variables' => $variables,
                    'template' => $variablesData['template'],
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch GIRAS configuration', [
                'error' => $e->getMessage()
            ]);
        }

        return Inertia::render('Admin/Invoices/Create', [
            'properties' => $properties,
            'selectedProperty' => $property ? [
                'id' => $property->id,
                'number_type' => $property->number_type,
                'number_value' => $property->number_value,
                'address' => $property->address,
                'amount' => $property->amount,
                'payment_frequency' => $property->payment_frequency,
                'property_type' => $property->propertyType ? $property->propertyType->name : null,
                'customer' => [
                    'id' => $property->customer->id,
                    'name' => $property->customer_type === 'App\\Models\\IndividualCustomer'
                        ? $property->customer->first_name . ' ' . $property->customer->last_name
                        : $property->customer->company_name,
                    'type' => $property->customer_type,
                ],
            ] : null,
            'girasConfig' => $girasConfig,
        ]);
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'sync_with_giras' => 'boolean',
            'giras_gateway' => 'nullable|string|in:paystack,remita,interswitch',
            'revenue_sub_head_key' => 'nullable|string', // For future flexibility
            'giras_variables' => 'nullable|array', // GIRAS template variables
        ]);

        try {
            DB::beginTransaction();

            $property = Property::with('customer')->findOrFail($validated['property_id']);

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'number_type' => $property->number_type,
                'number_value' => $property->number_value,
                'amount' => $validated['amount'],
                'paid_amount' => 0,
                'currency' => 'NGN',
                'issue_date' => now(),
                'due_date' => $validated['due_date'],
                'customer_id' => $property->customer_id,
                'customer_type' => $property->customer_type,
                'property_id' => $property->id,
                'issuer_id' => auth()->id(),
                'payment_status' => 'PENDING',
                'description' => $validated['description'] ?? "Invoice for {$property->number_type}: {$property->number_value}",
            ]);

            // Sync with GIRAS if requested
            if ($validated['sync_with_giras'] ?? true) { // Default to true
                try {
                    $this->syncInvoiceWithGiras($invoice, $property, $validated);
                } catch (Exception $e) {
                    // Log error but don't fail the invoice creation
                    Log::error('GIRAS Sync Failed', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Continue without GIRAS sync
                }
            }

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice created successfully!' . ($invoice->isSyncedWithGiras() ? ' Synced with GIRAS.' : ''));

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Invoice Creation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Sync invoice with GIRAS payment gateway
     */
    protected function syncInvoiceWithGiras(Invoice $invoice, Property $property, array $validated): void
    {
        // Get revenue sub head ID (use custom key if provided, otherwise use default)
        $revenueSubHeadKey = $validated['revenue_sub_head_key'] ?? 'default';
        $revenueSubHeadId = $this->girasService->getRevenueSubHeadId($revenueSubHeadKey);

        if (!$revenueSubHeadId) {
            throw new Exception("Revenue sub head not configured for key: {$revenueSubHeadKey}");
        }

        // Get gateway (use provided or default)
        $gateway = $validated['giras_gateway'] ?? $this->girasService->getDefaultGateway();

        // Get GIRAS variables from request or use amount as default
        $girasVariables = $validated['giras_variables'] ?? ['amount' => $validated['amount']];

        // Prepare taxpayer data (unregistered taxpayer)
        $customer = $property->customer;

        // Get phone number - use customer's phone or a default placeholder
        $phoneNumber = $customer->phone_number ?? '';
        Log::info('Phone Number: ' . $phoneNumber);
        if (empty($phoneNumber)) {
            // GIRAS requires phone number, use a placeholder if not available
            $phoneNumber = '+2340000000000'; // Placeholder for missing phone numbers
        }

        $taxpayerData = [
            'first_name' => $customer->first_name ?? $customer->name ?? 'N/A',
            'last_name' => $customer->last_name ?? '',
            'phone_number' => $phoneNumber,
            'email' => $customer->email ?? null,
        ];

        // Prepare entries for GIRAS
        $entries = [
            [
                'revenue_sub_head_id' => $revenueSubHeadId,
                'variables' => $girasVariables,
                'description' => $invoice->description,
                'due_date' => $invoice->due_date->format('Y-m-d'),
            ]
        ];

        // Create invoice in GIRAS with payment gateway
        $girasResponse = $this->girasService->createMultiTaxInvoices($entries, $taxpayerData, $gateway);

        // Update invoice with GIRAS data
        // Response is a collection of TaxInvoiceResource objects with 'data' wrapper
        if (isset($girasResponse['data']) && count($girasResponse['data']) > 0) {
            $invoice->update([
                'giras_gateway' => $gateway,
                'giras_response' => $girasResponse,
                'giras_synced_at' => now(),
            ]);

            Log::info('Invoice synced with GIRAS', [
                'invoice_id' => $invoice->id,
                'giras_response' => $girasResponse,
            ]);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'property.propertyType', 'issuer', 'payments']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'number_type' => $invoice->number_type,
                'number_value' => $invoice->number_value,
                'amount' => $invoice->amount,
                'paid_amount' => $invoice->paid_amount,
                'remaining_amount' => $invoice->amount - $invoice->paid_amount,
                'currency' => $invoice->currency,
                'issue_date' => $invoice->issue_date->format('Y-m-d'),
                'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                'payment_status' => $invoice->payment_status,
                'description' => $invoice->description,
                'is_overdue' => $invoice->isOverdue(),
                'customer' => [
                    'id' => $invoice->customer->id,
                    'name' => $invoice->customer_type === 'App\\Models\\IndividualCustomer'
                        ? $invoice->customer->first_name . ' ' . $invoice->customer->last_name
                        : $invoice->customer->company_name,
                    'type' => $invoice->customer_type === 'App\\Models\\IndividualCustomer' ? 'Individual' : 'Corporate',
                    'email' => $invoice->customer->email,
                    'phone' => $invoice->customer->phone,
                ],
                'property' => $invoice->property ? [
                    'id' => $invoice->property->id,
                    'number_type' => $invoice->property->number_type,
                    'number_value' => $invoice->property->number_value,
                    'address' => $invoice->property->address,
                    'property_type' => $invoice->property->propertyType ? $invoice->property->propertyType->name : null,
                    'amount' => $invoice->property->amount,
                    'payment_frequency' => $invoice->property->payment_frequency,
                ] : null,
                'issuer' => $invoice->issuer ? [
                    'id' => $invoice->issuer->id,
                    'name' => $invoice->issuer->name,
                ] : null,
                'giras' => [
                    'synced' => $invoice->isSyncedWithGiras(),
                    'invoice_id' => $invoice->giras_invoice_id,
                    'invoice_number' => $invoice->giras_invoice_number,
                    'reference' => $invoice->giras_reference,
                    'payment_url' => $invoice->giras_payment_url,
                    'gateway' => $invoice->giras_gateway,
                    'synced_at' => $invoice->giras_synced_at ? $invoice->giras_synced_at->format('Y-m-d H:i:s') : null,
                ],
                'payments' => $invoice->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'reference' => $payment->reference,
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'payment_date' => $payment->payment_date,
                        'gateway' => $payment->gateway,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified invoice
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load(['customer', 'property.propertyType']);

        $properties = Property::with(['customer', 'propertyType'])
            ->get()
            ->map(function ($prop) {
                return [
                    'id' => $prop->id,
                    'number_type' => $prop->number_type,
                    'number_value' => $prop->number_value,
                    'address' => $prop->address,
                    'amount' => $prop->amount,
                    'payment_frequency' => $prop->payment_frequency,
                    'property_type' => $prop->propertyType ? $prop->propertyType->name : null,
                    'customer' => [
                        'id' => $prop->customer->id,
                        'name' => $prop->customer_type === 'App\\Models\\IndividualCustomer'
                            ? $prop->customer->first_name . ' ' . $prop->customer->last_name
                            : $prop->customer->company_name,
                        'type' => $prop->customer_type,
                    ],
                ];
            });

        return Inertia::render('Admin/Invoices/Edit', [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'property_id' => $invoice->property_id,
                'amount' => $invoice->amount,
                'due_date' => $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null,
                'description' => $invoice->description,
                'payment_status' => $invoice->payment_status,
                'property' => [
                    'id' => $invoice->property->id,
                    'number_type' => $invoice->property->number_type,
                    'number_value' => $invoice->property->number_value,
                    'address' => $invoice->property->address,
                    'amount' => $invoice->property->amount,
                    'property_type' => $invoice->property->propertyType ? $invoice->property->propertyType->name : null,
                ],
            ],
            'properties' => $properties,
        ]);
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Only allow editing if invoice is not paid
        if ($invoice->payment_status === 'PAID') {
            return back()->withErrors(['error' => 'Cannot edit a paid invoice.']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $invoice->update([
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'description' => $validated['description'],
            ]);

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice updated successfully!');

        } catch (Exception $e) {
            Log::error('Invoice Update Error', [
                'invoice_id' => $invoice->id,
                'message' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to update invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Initiate payment for an invoice
     */
    public function initiatePayment(Invoice $invoice)
    {
        try {
            // Check if invoice is already paid
            if ($invoice->payment_status === 'PAID') {
                return back()->with('error', 'Invoice is already paid.');
            }

            // Check if invoice is synced with GIRAS
            if (!$invoice->isSyncedWithGiras()) {
                return back()->with('error', 'Invoice must be synced with GIRAS before payment can be initiated.');
            }

            // Get gateway from invoice or default to paystack
            $gateway = $invoice->giras_gateway ?? 'paystack';

            // Generate callback URL
            $callbackUrl = route('admin.invoices.giras-callback', ['invoice' => $invoice->id]);

            $girasInvoices = $invoice->giras_response['data'];
            $girasInvoiceIds = array_column($girasInvoices, 'id');
            // Initiate payment with GIRAS
            $paymentData = $this->girasService->initiatePayment(
                $girasInvoiceIds,
                $gateway,
                null, // No custom reference
                $callbackUrl
            );

            Log::info('Payment Initiated from Pay Now Button', [
                'invoice_id' => $invoice->id,
                'giras_invoice_id' => $invoice->giras_invoice_id,
                'payment_data' => $paymentData,
            ]);

            // Create Payment record in GOGIS
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'property_id' => $invoice->property_id,
                'customer_id' => $invoice->customer_id,
                'customer_type' => $invoice->customer_type,
                'amount' => $invoice->amount,
                'paid_amount' => 0.00,
                'charges' => 0.00,
                'gateway' => $gateway,
                'reference' => $paymentData['reference'] ?? null,
                'status' => 'PENDING',
                'payment_date' => now(),
            ]);

            // Update invoice with payment URL and reference
            $invoice->update([
                'giras_payment_url' => $paymentData['link'] ?? null,
                'giras_reference' => $paymentData['reference'] ?? null,
            ]);

            Log::info('Payment Record Created', [
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'reference' => $payment->reference,
            ]);

            // Return payment data to frontend for dialog display
            return back()->with([
                'success' => 'Payment initiated successfully.',
                'paymentData' => $paymentData,
            ]);

        } catch (Exception $e) {
            Log::error('Payment Initiation Failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Revalidate payment status for a specific payment
     */
    public function revalidatePayment(Invoice $invoice, Payment $payment)
    {
        try {
            Log::info('Payment Revalidation Request', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'reference' => $payment->reference,
            ]);

            // Check if payment belongs to this invoice
            if ($payment->invoice_id !== $invoice->id) {
                return back()->with('error', 'Payment does not belong to this invoice.');
            }

            // Check if payment is already successful
            if ($payment->status === 'SUCCESS') {
                return back()->with('info', 'Payment is already marked as successful.');
            }

            // Verify payment status with GIRAS
            $verificationResponse = $this->girasService->verifyPayment($payment->reference);

            Log::info('Payment Revalidation Response', [
                'payment_id' => $payment->id,
                'reference' => $payment->reference,
                'verification_response' => $verificationResponse,
            ]);

            // Extract payment status from GIRAS response
            $girasPaymentStatus = $verificationResponse['status'] ?? null;
            $girasPaymentData = $verificationResponse;

            // Map GIRAS status to GOGIS status
            $paymentStatus = 'PENDING';

            if ($girasPaymentStatus === 'success' || $girasPaymentStatus === 'SUCCESS') {
                $paymentStatus = 'SUCCESS';
            } elseif ($girasPaymentStatus === 'failed' || $girasPaymentStatus === 'FAILED') {
                $paymentStatus = 'FAILED';
            }

            // Update payment record
            $payment->update([
                'status' => $paymentStatus,
                'paid_amount' => $paymentStatus === 'SUCCESS' ? $payment->amount : 0,
                'charges' => $girasPaymentData['charges'] ?? 0,
                'channel' => $girasPaymentData['channel'] ?? null,
                'payment_date' => $paymentStatus === 'SUCCESS' ? now() : $payment->payment_date,
            ]);

            Log::info('Payment Record Updated via Revalidation', [
                'payment_id' => $payment->id,
                'status' => $paymentStatus,
                'paid_amount' => $payment->paid_amount,
            ]);

            // Update invoice if payment was successful
            if ($paymentStatus === 'SUCCESS') {
                // Calculate new paid amount
                $newPaidAmount = $invoice->paid_amount + $payment->paid_amount;
                $newRemainingAmount = max(0, $invoice->amount - $newPaidAmount);

                // Determine invoice payment status
                $newInvoiceStatus = $newRemainingAmount <= 0 ? 'PAID' : 'PARTIAL';

                $invoice->update([
                    'payment_status' => $newInvoiceStatus,
                    'paid_amount' => $newPaidAmount,
                ]);

                Log::info('Invoice Updated After Revalidation', [
                    'invoice_id' => $invoice->id,
                    'payment_status' => $newInvoiceStatus,
                    'paid_amount' => $invoice->paid_amount,
                    'remaining_amount' => $invoice->amount - $invoice->paid_amount,
                ]);

                return back()->with('success', 'Payment verified successfully! Invoice has been updated.');
            } elseif ($paymentStatus === 'FAILED') {
                return back()->with('error', 'Payment verification failed. The payment was not successful.');
            } else {
                return back()->with('info', 'Payment is still pending. Please try again later.');
            }

        } catch (Exception $e) {
            Log::error('Payment Revalidation Failed', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to revalidate payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle GIRAS payment callback
     */
    public function girasCallback(Invoice $invoice, Request $request)
    {
        try {
            Log::info('GIRAS Payment Callback Received', [
                'invoice_id' => $invoice->id,
                'query_params' => $request->all(),
            ]);

            // Get reference from query params (GIRAS sends it as 'reference')
            $reference = $request->query('reference');

            if (!$reference) {
                Log::warning('GIRAS Callback: No reference provided', [
                    'invoice_id' => $invoice->id,
                    'query_params' => $request->all(),
                ]);

                return redirect()->route('admin.invoices.show', $invoice)
                    ->with('warning', 'Payment reference not found in callback.');
            }

            // Verify payment status with GIRAS
            $verificationResponse = $this->girasService->verifyPayment($reference);

            Log::info('GIRAS Payment Verification for Callback', [
                'invoice_id' => $invoice->id,
                'reference' => $reference,
                'verification_response' => $verificationResponse,
            ]);

            // Find the payment record by reference
            $payment = Payment::where('reference', $reference)->first();

            if (!$payment) {
                Log::error('GIRAS Callback: Payment record not found', [
                    'invoice_id' => $invoice->id,
                    'reference' => $reference,
                ]);

                return redirect()->route('admin.invoices.show', $invoice)
                    ->with('error', 'Payment record not found.');
            }

            // Extract payment status from GIRAS response
            // GIRAS returns payment data with status field
            $girasPaymentStatus = $verificationResponse['status'] ?? null;
            $girasPaymentData = $verificationResponse;

            // Map GIRAS status to GOGIS status
            // GIRAS statuses: 'success', 'pending', 'failed'
            $paymentStatus = 'PENDING';
            $invoicePaymentStatus = 'PENDING';

            if ($girasPaymentStatus === 'success' || $girasPaymentStatus === 'SUCCESS') {
                $paymentStatus = 'SUCCESS';
                $invoicePaymentStatus = 'PAID';
            } elseif ($girasPaymentStatus === 'failed' || $girasPaymentStatus === 'FAILED') {
                $paymentStatus = 'FAILED';
                $invoicePaymentStatus = 'PENDING'; // Keep invoice as pending if payment failed
            }

            // Update payment record
            $payment->update([
                'status' => $paymentStatus,
                'paid_amount' => $paymentStatus === 'SUCCESS' ? $payment->amount : 0,
                'charges' => $girasPaymentData['charges'] ?? 0,
                'channel' => $girasPaymentData['channel'] ?? null,
                'payment_date' => $paymentStatus === 'SUCCESS' ? now() : $payment->payment_date,
            ]);

            Log::info('Payment Record Updated', [
                'payment_id' => $payment->id,
                'status' => $paymentStatus,
                'paid_amount' => $payment->paid_amount,
            ]);

            // Update invoice if payment was successful
            if ($paymentStatus === 'SUCCESS') {
                $newPaidAmount = $invoice->paid_amount + $payment->paid_amount;

                $invoice->update([
                    'payment_status' => $invoicePaymentStatus,
                    'paid_amount' => $newPaidAmount,
                ]);

                Log::info('Invoice Updated After Successful Payment', [
                    'invoice_id' => $invoice->id,
                    'payment_status' => $invoicePaymentStatus,
                    'paid_amount' => $invoice->paid_amount,
                    'remaining_amount' => $invoice->amount - $invoice->paid_amount,
                ]);

                return redirect()->route('admin.invoices.show', $invoice)
                    ->with('success', 'Payment successful! Invoice has been marked as paid.');
            } elseif ($paymentStatus === 'FAILED') {
                return redirect()->route('admin.invoices.show', $invoice)
                    ->with('error', 'Payment failed. Please try again.');
            } else {
                return redirect()->route('admin.invoices.show', $invoice)
                    ->with('info', 'Payment is still pending. Please wait for confirmation.');
            }

        } catch (Exception $e) {
            Log::error('GIRAS Callback Processing Failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('error', 'Failed to process payment callback: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        // Only allow deletion if invoice is not paid
        if ($invoice->payment_status === 'PAID') {
            return back()->withErrors(['error' => 'Cannot delete a paid invoice.']);
        }

        try {
            $invoice->delete();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice deleted successfully!');

        } catch (Exception $e) {
            Log::error('Invoice Deletion Error', [
                'invoice_id' => $invoice->id,
                'message' => $e->getMessage()
            ]);

            return back()->withErrors(['error' => 'Failed to delete invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/INV-(\d{4})(\d{2})-(\d{4})/', $lastInvoice->invoice_number, $matches)) {
            $lastNumber = (int)$matches[3];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $newNumber);
    }
}