<?php

use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\EventController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\NewsController;
use Illuminate\Support\Facades\Route;

// Public Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// News Routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news:slug}', [NewsController::class, 'show'])->name('news.show');

// Events Routes
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin Dashboard (protected by auth middleware)
Route::middleware(['auth:web'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return inertia('Admin/Dashboard');
    })->name('dashboard');

    // Settings Management
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update']); // For file uploads with _method spoofing

    // Carousel Management
    Route::resource('carousel', App\Http\Controllers\Admin\CarouselController::class);
    Route::post('/carousel/toggle', [App\Http\Controllers\Admin\CarouselController::class, 'toggleCarousel'])->name('carousel.toggle');
    Route::post('/carousel/reorder', [App\Http\Controllers\Admin\CarouselController::class, 'reorder'])->name('carousel.reorder');

    // Pages Management
    Route::resource('pages', App\Http\Controllers\Admin\PageController::class);

    // News Management
    Route::resource('news', App\Http\Controllers\Admin\NewsController::class);

    // Events Management
    Route::resource('events', App\Http\Controllers\Admin\EventController::class);

    // Media Gallery Management
    Route::get('/media', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{id}', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

    // Customers Management
    Route::get('/customers/search', [App\Http\Controllers\Admin\CustomerSearchController::class, 'search'])->name('customers.search');
    Route::resource('individual-customers', App\Http\Controllers\Admin\IndividualCustomerController::class);
    Route::get('/individual-customers/{individualCustomer}/property-payments', [App\Http\Controllers\Admin\IndividualCustomerController::class, 'propertyPayments'])->name('individual-customers.property-payments');
    Route::resource('corporate-customers', App\Http\Controllers\Admin\CorporateCustomerController::class);
    Route::get('/corporate-customers/{corporateCustomer}/property-payments', [App\Http\Controllers\Admin\CorporateCustomerController::class, 'propertyPayments'])->name('corporate-customers.property-payments');

    // Property Types
    Route::resource('property-types', App\Http\Controllers\Admin\PropertyTypeController::class);

    // Property Profiling
    Route::resource('properties', App\Http\Controllers\Admin\PropertyController::class);

    // Invoices Management
    Route::resource('invoices', App\Http\Controllers\Admin\InvoiceController::class);
    Route::post('/invoices/{invoice}/initiate-payment', [App\Http\Controllers\Admin\InvoiceController::class, 'initiatePayment'])->name('invoices.initiate-payment');
    Route::post('/invoices/{invoice}/payments/{payment}/revalidate', [App\Http\Controllers\Admin\InvoiceController::class, 'revalidatePayment'])->name('invoices.payments.revalidate');
    Route::get('/invoices/{invoice}/giras-callback', [App\Http\Controllers\Admin\InvoiceController::class, 'girasCallback'])->name('invoices.giras-callback');

    // Payments Management
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');

    // Revenue Heads Management
    Route::resource('revenue-heads', App\Http\Controllers\Admin\RevenueHeadController::class);

    // Admins Management
    Route::resource('admins', App\Http\Controllers\Admin\AdminController::class);

    // Activity Logs
    Route::get('/logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/settings.php';
