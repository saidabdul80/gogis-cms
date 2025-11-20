<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndividualCustomer;
use App\Models\CorporateCustomer;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Lga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with comprehensive analytics
     */
    public function index()
    {
        // Overview Metrics
        $totalIndividualCustomers = IndividualCustomer::count();
        $totalCorporateCustomers = CorporateCustomer::count();
        $totalCustomers = $totalIndividualCustomers + $totalCorporateCustomers;
        $totalProperties = Property::count();
        $totalInvoices = Invoice::count();
        $totalRevenue = Payment::where('status', 'SUCCESS')->sum('paid_amount');

        // Invoice Statistics
        $paidInvoices = Invoice::where('payment_status', 'PAID')->count();
        $pendingInvoices = Invoice::where('payment_status', 'PENDING')->count();
        $partialInvoices = Invoice::where('payment_status', 'PARTIAL')->count();
        $overdueInvoices = Invoice::where('payment_status', '!=', 'PAID')
            ->where('due_date', '<', now())
            ->count();

        // Revenue Statistics
        $totalInvoiceAmount = Invoice::sum('amount');
        $totalPaidAmount = Invoice::sum('paid_amount');
        $totalOutstanding = $totalInvoiceAmount - $totalPaidAmount;

        // Monthly Revenue Trend (Last 12 months)
        $monthlyRevenue = Payment::where('status', 'SUCCESS')
            ->where('payment_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(paid_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->month . '-01')->format('M Y'),
                    'total' => (float) $item->total,
                ];
            });

        // Payment Status Distribution
        $paymentStatusDistribution = [
            ['status' => 'SUCCESS', 'count' => Payment::where('status', 'SUCCESS')->count()],
            ['status' => 'PENDING', 'count' => Payment::where('status', 'PENDING')->count()],
            ['status' => 'FAILED', 'count' => Payment::where('status', 'FAILED')->count()],
        ];

        // Properties by Type
        $propertiesByType = Property::select('property_type_id', DB::raw('count(*) as count'))
            ->with('propertyType:id,name')
            ->groupBy('property_type_id')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->propertyType->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        // Properties by LGA (Top 10) - Temporarily disabled until lga_id is added
        $propertiesByLga = [];

        // Recent Invoices (Last 10)
        $recentInvoices = Invoice::with(['property:id,number_type,number_value', 'customer'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_name' => $invoice->customer->name ?? $invoice->customer->first_name . ' ' . $invoice->customer->last_name,
                    'property' => $invoice->property ? $invoice->property->number_type . ': ' . $invoice->property->number_value : 'N/A',
                    'amount' => $invoice->amount,
                    'payment_status' => $invoice->payment_status,
                    'created_at' => $invoice->created_at->format('Y-m-d H:i'),
                ];
            });

        // Recent Payments (Last 10)
        $recentPayments = Payment::with(['invoice:id,invoice_number', 'customer'])
            ->orderByDesc('payment_date')
            ->limit(10)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'reference' => $payment->reference,
                    'invoice_number' => $payment->invoice->invoice_number ?? 'N/A',
                    'customer_name' => $payment->customer->name ?? $payment->customer->first_name . ' ' . $payment->customer->last_name,
                    'amount' => $payment->paid_amount,
                    'gateway' => $payment->gateway,
                    'status' => $payment->status,
                    'payment_date' => $payment->payment_date ? Carbon::parse($payment->payment_date)->format('Y-m-d H:i') : 'N/A',
                ];
            });

        // Recent Customers (Last 10)
        $recentIndividualCustomers = IndividualCustomer::orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'type' => 'Individual',
                    'name' => $customer->first_name . ' ' . $customer->last_name,
                    'email' => $customer->email,
                    'phone' => $customer->phone_number,
                    'created_at' => $customer->created_at->format('Y-m-d H:i'),
                ];
            });

        $recentCorporateCustomers = CorporateCustomer::orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'type' => 'Corporate',
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone_number,
                    'created_at' => $customer->created_at->format('Y-m-d H:i'),
                ];
            });

        $recentCustomers = $recentIndividualCustomers->concat($recentCorporateCustomers)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return Inertia::render('Admin/Dashboard', [
            'analytics' => [
                // Overview Metrics
                'overview' => [
                    'totalCustomers' => $totalCustomers,
                    'totalIndividualCustomers' => $totalIndividualCustomers,
                    'totalCorporateCustomers' => $totalCorporateCustomers,
                    'totalProperties' => $totalProperties,
                    'totalInvoices' => $totalInvoices,
                    'totalRevenue' => $totalRevenue,
                    'paidInvoices' => $paidInvoices,
                    'pendingInvoices' => $pendingInvoices,
                    'partialInvoices' => $partialInvoices,
                    'overdueInvoices' => $overdueInvoices,
                ],

                // Revenue Statistics
                'revenue' => [
                    'totalInvoiceAmount' => $totalInvoiceAmount,
                    'totalPaidAmount' => $totalPaidAmount,
                    'totalOutstanding' => $totalOutstanding,
                    'collectionRate' => $totalInvoiceAmount > 0 ? round(($totalPaidAmount / $totalInvoiceAmount) * 100, 2) : 0,
                ],

                // Charts Data
                'charts' => [
                    'monthlyRevenue' => $monthlyRevenue,
                    'paymentStatusDistribution' => $paymentStatusDistribution,
                    'propertiesByType' => $propertiesByType,
                    'propertiesByLga' => $propertiesByLga,
                ],

                // Recent Activities
                'recent' => [
                    'invoices' => $recentInvoices,
                    'payments' => $recentPayments,
                    'customers' => $recentCustomers,
                ],
            ],
        ]);
    }
}
