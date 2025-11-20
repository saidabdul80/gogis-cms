<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'number_type',
        'number_value',
        'amount',
        'paid_amount',
        'charges',
        'currency',
        'issue_date',
        'due_date',
        'customer_id',
        'customer_type',
        'property_id',
        'issuer_id',
        'payment_status',
        'template',
        'variables',
        'description',
        'download_count',
        'invoice_type_id',
        'giras_invoice_id',
        'giras_invoice_number',
        'giras_reference',
        'giras_payment_url',
        'giras_gateway',
        'giras_response',
        'giras_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'charges' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'variables' => 'array',
            'giras_response' => 'array',
            'giras_synced_at' => 'datetime',
        ];
    }


    // Relationships
    public function customer()
    {
        return $this->morphTo();
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function issuer()
    {
        return $this->belongsTo(Admin::class, 'issuer_id');
    }

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('payment_status', 'PENDING');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'PAID');
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', '!=', 'PAID')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now());
    }

    // Helper methods
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'PAID';
    }

    public function isOverdue(): bool
    {
        return $this->due_date &&
               $this->due_date->isPast() &&
               !$this->isFullyPaid();
    }

    public function isSyncedWithGiras(): bool
    {
        return !is_null($this->giras_invoice_id) && !is_null($this->giras_synced_at);
    }

    public function hasPaymentUrl(): bool
    {
        return !is_null($this->giras_payment_url);
    }

    //update invoice_id and handle error
      public function getGirasInvoiceIdAttribute()
    {
        if(isset($this->giras_response['data'][0]['id'])){
            return $this->giras_response['data'][0]['id'];
        }else{
            return $this->giras_invoice_id;
        }   
    }

    public function getGirasInvoiceNumberAttribute()
    {
        if(isset($this->giras_response['data'][0]['invoice_number'])){
            return $this->giras_response['data'][0]['invoice_number'];
        }else{
            return $this->giras_invoice_number;
        }   
    }
}
