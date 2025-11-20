<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'template',
        'currency',
        'region_type',
        'frequency',
        'start_date',
        'end_date',
        'revenue_sub_head_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    // Relationships
    public function revenueSubHead()
    {
        return $this->belongsTo(RevenueSubHead::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Scope for active invoice types
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }
}
