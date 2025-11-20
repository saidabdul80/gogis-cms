# GIRAS Payment Gateway Integration Guide

## Overview

GOGIS is integrated with GIRAS (Gombe State Internal Revenue Service) payment gateway to enable seamless invoice creation and payment processing. This document explains the integration architecture and how to extend it in the future.

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
# GIRAS Payment Gateway Configuration
GIRAS_BASE_URL=https://giras-backend.staging.irs.gm.gov.ng
GIRAS_CLIENT_ID=15
GIRAS_SECRET_KEY=imoggtPWeXZOuiNiggkOzSDgukDIKGKLU6A1CZZl

# GIRAS Revenue Sub Heads
GIRAS_DEFAULT_REVENUE_SUB_HEAD=1052
GIRAS_PROPERTY_TAX_REVENUE_SUB_HEAD=1052

# GIRAS Payment Gateway
GIRAS_DEFAULT_GATEWAY=paystack
```

### Revenue Sub Heads Configuration

The system is designed to support multiple revenue sub heads. Currently configured:

- **Property Tax**: Revenue Sub Head ID `1052` (default)

#### Adding New Revenue Sub Heads

To add new revenue sub heads in the future:

1. **Update `config/services.php`:**

```php
'revenue_sub_heads' => [
    'default' => env('GIRAS_DEFAULT_REVENUE_SUB_HEAD', 1052),
    'property_tax' => env('GIRAS_PROPERTY_TAX_REVENUE_SUB_HEAD', 1052),
    'land_use_charge' => env('GIRAS_LAND_USE_CHARGE_REVENUE_SUB_HEAD', 1053), // New
    'development_levy' => env('GIRAS_DEVELOPMENT_LEVY_REVENUE_SUB_HEAD', 1054), // New
],
```

2. **Add to `.env`:**

```env
GIRAS_LAND_USE_CHARGE_REVENUE_SUB_HEAD=1053
GIRAS_DEVELOPMENT_LEVY_REVENUE_SUB_HEAD=1054
```

3. **Use in code:**

```php
// Get specific revenue sub head
$revenueSubHeadId = $this->girasService->getRevenueSubHeadId('land_use_charge');

// Or pass as parameter when creating invoice
$validated['revenue_sub_head_key'] = 'land_use_charge';
```

## Architecture

### Service Layer

**`App\Services\GirasService`** - Handles all GIRAS API communication

Key methods:
- `createMultiTaxInvoices()` - Create invoices with payment gateway
- `getRevenueSubHeadId($key)` - Get revenue sub head ID by key
- `getAllRevenueSubHeads()` - Get all configured revenue sub heads
- `getDefaultGateway()` - Get default payment gateway
- `buildTaxpayerData($customer)` - Map GOGIS customer to GIRAS taxpayer

### Database Schema

**Invoices Table** - GIRAS-specific fields:

| Field | Type | Description |
|-------|------|-------------|
| `giras_invoice_id` | bigInteger | GIRAS invoice ID |
| `giras_invoice_number` | string | GIRAS invoice number |
| `giras_reference` | string | Payment reference |
| `giras_payment_url` | text | Payment gateway URL |
| `giras_gateway` | string | Gateway name (paystack/remita/interswitch) |
| `giras_response` | json | Full GIRAS API response |
| `giras_synced_at` | timestamp | Sync timestamp |

### Invoice Creation Flow

1. User creates invoice in GOGIS
2. System generates GOGIS invoice number
3. If GIRAS sync enabled:
   - Map GOGIS customer to GIRAS taxpayer
   - Get revenue sub head ID (default: 1052)
   - Call GIRAS API to create invoice
   - Store GIRAS response in invoice record
   - Generate payment URL
4. User can pay via GIRAS payment gateway

## Payment Gateways

Supported gateways:
- **Paystack** (default)
- **Remita**
- **Interswitch**

## Customer Mapping

GOGIS customers are mapped to GIRAS taxpayers:

| GOGIS Customer Type | GIRAS Taxpayer Type |
|---------------------|---------------------|
| `IndividualCustomer` | `App\Models\IndividualTaxPayer` |
| `CorporateCustomer` | `App\Models\CorporateTaxPayer` |

## Usage Examples

### Creating Invoice with GIRAS Sync

```php
// In controller
$invoice = Invoice::create([...]);

// Sync with GIRAS
$this->syncInvoiceWithGiras($invoice, $property, [
    'revenue_sub_head_key' => 'property_tax', // or 'default'
    'giras_gateway' => 'paystack',
]);
```

### Checking GIRAS Sync Status

```php
if ($invoice->isSyncedWithGiras()) {
    $paymentUrl = $invoice->giras_payment_url;
    // Redirect user to payment
}
```

### Getting Revenue Sub Head

```php
// Get default
$defaultId = $girasService->getRevenueSubHeadId(); // 1052

// Get specific
$propertyTaxId = $girasService->getRevenueSubHeadId('property_tax'); // 1052

// Get all
$allSubHeads = $girasService->getAllRevenueSubHeads();
// ['default' => 1052, 'property_tax' => 1052, ...]
```

## Future Enhancements

### Multiple Revenue Sub Heads

When you need to support different types of charges:

1. Add new revenue sub head to config
2. Update invoice creation form to allow selection
3. Pass `revenue_sub_head_key` in request

### Payment Callbacks

Implement webhook endpoint to receive payment status updates from GIRAS:

```php
Route::post('/giras/callback', [InvoiceController::class, 'handleGirasCallback']);
```

## Troubleshooting

### Invoice not syncing with GIRAS

Check logs: `storage/logs/laravel.log`

Common issues:
- Invalid GIRAS credentials
- Revenue sub head not configured
- Network connectivity issues

### Payment URL not generated

Ensure:
- `sync_with_giras` is true
- Valid gateway selected
- GIRAS API is accessible

## Support

For GIRAS API documentation, refer to:
`giras/GIRAS_COMPREHENSIVE_API_DOCUMENTATION.md`

