<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'payment_date',
        'amount',
        'paid_amount',
        'charges',
        'status',
        'gateway',
        'channel',
        'redirect',
        'customer_type',
        'customer_id',
        'property_id',
        'invoice_id',
        'revenue_sub_head_id',
        'revenue_head_id',
        'revenue_source_id',
        'revenue_source_type',
        'description',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'charges' => 'decimal:2',
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function revenueSubHead()
    {
        return $this->belongsTo(RevenueSubHead::class);
    }

    public function revenueHead()
    {
        return $this->belongsTo(RevenueHead::class);
    }

    public function revenueSource()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'SUCCESS');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'FAILED');
    }
}
