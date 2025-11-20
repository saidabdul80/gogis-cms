<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueSubHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'revenue_head_id',
        'name',
        'code',
        'remark',
    ];

    // Relationships
    public function revenueHead()
    {
        return $this->belongsTo(RevenueHead::class);
    }

    public function invoiceTypes()
    {
        return $this->hasMany(InvoiceType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
