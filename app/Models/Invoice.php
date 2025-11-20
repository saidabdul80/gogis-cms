<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'amount',
        'paid_amount',
        'charges',
        'currency',
        'issue_date',
        'due_date',
        'taxpayer_id',
        'issuer_id',
        'payment_status',
        'template',
        'variables',
        'description',
        'download_count',
        'invoice_type_id',
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
        ];
    }

    // Relationships
    public function taxpayer()
    {
        return $this->belongsTo(Taxpayer::class);
    }

    public function issuer()
    {
        return $this->belongsTo(Admin::class, 'issuer_id');
    }

    public function invoiceType()
    {
        return $this->belongsTo(InvoiceType::class);
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
}
