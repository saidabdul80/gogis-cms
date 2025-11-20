# GIRAS Payment Integration Guide

## Overview

This document explains how GOGIS integrates with GIRAS for automatic payment initiation after invoice creation.

## Payment Flow

### 1. Invoice Creation
When an invoice is created in GOGIS with GIRAS sync enabled:

1. **Create Invoice in GOGIS** → Invoice record created in `invoices` table
2. **Sync with GIRAS** → Invoice created in GIRAS via `/api/multi_tax_invoices` endpoint
3. **Initiate Payment** → Payment automatically initiated via `/api/pay` endpoint
4. **Store Payment Details** → Payment URL and reference stored in GOGIS invoice

### 2. Payment Initiation

After successfully creating an invoice in GIRAS, GOGIS automatically calls the GIRAS `/pay` endpoint:

**Endpoint:** `POST https://giras-backend.staging.irs.gm.gov.ng/api/pay`

**Headers:**
```
x-client-id: 15
x-secret-key: imoggtPWeXZOuiNiggkOzSDgukDIKGKLU6A1CZZl
Content-Type: application/json
Accept: application/json
```

**Request Payload:**
```json
{
  "invoice_id": [123],
  "gateway": "paystack",
  "callback_url": "https://gogis.gm.gov.ng/admin/invoices/5/giras-callback"
}
```

**Response Structure:**
```json
{
  "link": "https://checkout.paystack.com/abc123xyz",
  "reference": "GIRAS-REF-123456"
}
```

### 3. Gateway-Specific Responses

#### Paystack
- **Response:** `{ "link": "https://checkout.paystack.com/...", "reference": "..." }`
- **Action:** Redirect user to the `link` URL
- **User Experience:** Paystack checkout page opens in new tab

#### Remita
- **Response:** `{ "link": "1234567890", "reference": "1234567890" }`
- **Note:** The `link` is actually the RRR (Remita Retrieval Reference)
- **Action:** Construct Remita payment URL: `https://remitademo.net/remita/ecomm/finalize.reg?merchantId=MERCHANT_ID&hash=HASH&rrr=RRR`

#### Other Gateways (Interswitch, Etranzact, Paymish)
- Similar pattern with gateway-specific URLs

## Implementation Details

### Backend Changes

#### 1. GirasService.php
Updated `initiatePayment()` method to:
- Accept GIRAS invoice IDs, gateway, and callback URL
- Call GIRAS `/pay` endpoint
- Return structured response with `link`, `reference`, and `gateway`

#### 2. InvoiceController.php
Updated `syncInvoiceWithGiras()` method to:
- Create invoice in GIRAS
- Automatically initiate payment
- Store payment URL and reference in invoice
- Handle errors gracefully (log but don't fail invoice creation)

Added `girasCallback()` method to:
- Handle payment completion callbacks from GIRAS
- Log callback data for debugging
- Redirect to invoice show page

#### 3. Routes (web.php)
Added callback route:
```php
Route::get('/invoices/{invoice}/giras-callback', [InvoiceController::class, 'girasCallback'])
    ->name('invoices.giras-callback');
```

### Frontend Changes

#### Show.vue
Already includes "Pay Now" button that:
- Shows only when invoice is not paid AND payment URL exists
- Opens payment URL in new tab
- Uses success color and credit card icon

```vue
<v-btn
    v-if="invoice.payment_status !== 'PAID' && invoice.giras.payment_url"
    color="success"
    prepend-icon="mdi-credit-card"
    :href="invoice.giras.payment_url"
    target="_blank"
>
    Pay Now
</v-btn>
```

## Database Fields

The `invoices` table stores GIRAS payment data:

- `giras_invoice_id` - GIRAS tax invoice ID
- `giras_invoice_number` - GIRAS invoice number
- `giras_reference` - Payment reference from GIRAS
- `giras_payment_url` - Payment URL (Paystack checkout link or Remita RRR)
- `giras_gateway` - Payment gateway used (paystack, remita, etc.)
- `giras_response` - Full GIRAS API response (JSON)
- `giras_synced_at` - Timestamp of GIRAS sync

## User Experience

1. **Admin creates invoice** in GOGIS
2. **Invoice syncs with GIRAS** automatically
3. **Payment is initiated** automatically
4. **Admin sees "Pay Now" button** on invoice show page
5. **Admin clicks "Pay Now"** → Opens payment gateway in new tab
6. **User completes payment** on gateway (Paystack/Remita)
7. **Gateway redirects** to GIRAS callback
8. **GIRAS updates** payment status
9. **GIRAS redirects** to GOGIS callback URL
10. **GOGIS shows** payment confirmation

## Error Handling

- If GIRAS sync fails → Invoice still created, error logged
- If payment initiation fails → Invoice still synced, error logged
- Payment URL not available → "Pay Now" button hidden
- All errors logged with context for debugging

## Testing

To test the integration:

1. Create a new invoice with GIRAS sync enabled
2. Check logs for GIRAS API calls
3. Verify payment URL is stored in invoice
4. Click "Pay Now" button on invoice show page
5. Complete payment on gateway
6. Verify callback is received

## Configuration

GIRAS settings in `.env`:
```
GIRAS_BASE_URL=https://giras-backend.staging.irs.gm.gov.ng/api
GIRAS_SECRET_KEY=imoggtPWeXZOuiNiggkOzSDgukDIKGKLU6A1CZZl
GIRAS_CLIENT_ID=15
GIRAS_DEFAULT_GATEWAY=paystack
GIRAS_REVENUE_SUB_HEAD_DEFAULT=1052
```

