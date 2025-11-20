<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateCustomer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CorporateCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CorporateCustomer::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        $customers = $query->latest()->paginate(15);

        return Inertia::render('Admin/CorporateCustomers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CorporateCustomers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|in:Company,Hotel,Bank,Hospital,School,MDAs,Others',
            'registration_number' => 'required|string|unique:corporate_customers,registration_number',
            'registered_date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = CorporateCustomer::create($validated);

        // If this is an AJAX request (from CustomerSelector), return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'type' => 'corporate',
                    'email' => $customer->email,
                    'phone_number' => $customer->phone_number,
                    'customer_id' => $customer->id,
                    'customer_type' => 'App\\Models\\CorporateCustomer'
                ]
            ]);
        }

        return redirect()->route('admin.corporate-customers.index')
            ->with('success', 'Corporate customer created successfully.')
            ->with('customerId', $customer->id)
            ->with('customerType', 'corporate');
    }

    /**
     * Display the specified resource.
     */
    public function show(CorporateCustomer $corporateCustomer)
    {
        // Get properties with their types and latest invoice
        $properties = $corporateCustomer->properties()
            ->with(['propertyType', 'customer'])
            ->get()
            ->map(function ($property) {
                // Get the latest invoice for this specific property
                $latestInvoice = \App\Models\Invoice::where('property_id', $property->id)
                    ->latest('due_date')
                    ->first();

                return [
                    'id' => $property->id,
                    'number_type' => $property->number_type,
                    'number_value' => $property->number_value,
                    'address' => $property->address,
                    'property_type' => $property->propertyType,
                    'amount' => $property->amount,
                    'payment_frequency' => $property->payment_frequency,
                    'start_date' => $property->start_date,
                    'last_invoice_due_date' => $latestInvoice ? $latestInvoice->due_date : null,
                    'last_invoice_status' => $latestInvoice ? $latestInvoice->payment_status : null,
                ];
            });

        // Calculate total properties
        $totalProperties = $properties->count();

        // Calculate overall total paid amount
        $totalPaidAmount = $corporateCustomer->payments()
            ->where('status', 'SUCCESS')
            ->sum('paid_amount');

        // Group properties by property type
        $propertiesByType = $properties->groupBy('property_type.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
            ];
        });

        return Inertia::render('Admin/CorporateCustomers/Show', [
            'customer' => $corporateCustomer,
            'properties' => $properties,
            'analytics' => [
                'total_properties' => $totalProperties,
                'total_paid_amount' => $totalPaidAmount,
                'properties_by_type' => $propertiesByType,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CorporateCustomer $corporateCustomer)
    {
        return Inertia::render('Admin/CorporateCustomers/Edit', [
            'customer' => $corporateCustomer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CorporateCustomer $corporateCustomer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|in:Company,Hotel,Bank,Hospital,School,MDAs,Others',
            'registration_number' => 'required|string|unique:corporate_customers,registration_number,' . $corporateCustomer->id,
            'registered_date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $corporateCustomer->update($validated);

        return redirect()->route('admin.corporate-customers.index')
            ->with('success', 'Corporate customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CorporateCustomer $corporateCustomer)
    {
        $corporateCustomer->delete();

        return redirect()->route('admin.corporate-customers.index')
            ->with('success', 'Corporate customer deleted successfully.');
    }

    /**
     * Get payment history for a specific property
     */
    public function propertyPayments(Request $request, CorporateCustomer $corporateCustomer)
    {
        $propertyId = $request->input('property_id');

        if (!$propertyId) {
            return response()->json(['error' => 'Property ID is required'], 400);
        }

        // Verify the property belongs to this customer
        $property = $corporateCustomer->properties()->find($propertyId);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // Get last 5 payments for this specific property
        $payments = \App\Models\Payment::where('property_id', $propertyId)
            ->where('customer_id', $corporateCustomer->id)
            ->where('customer_type', get_class($corporateCustomer))
            ->where('status', 'SUCCESS')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(function ($payment) use ($property) {
                return [
                    'id' => $payment->id,
                    'reference' => $payment->reference,
                    'amount' => $payment->paid_amount,
                    'payment_date' => $payment->payment_date,
                    'frequency' => $property->payment_frequency,
                ];
            });

        return response()->json($payments);
    }
}
