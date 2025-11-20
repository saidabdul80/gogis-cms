<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_type_id',
        'address',
        'longitude',
        'latitude',
        'description',
        'number_type',
        'number_value',
        'start_date',
        'payment_frequency',
        'amount',
        'customer_id',
        'customer_type',
    ];

    protected function casts(): array
    {
        return [
            'longitude' => 'decimal:7',
            'latitude' => 'decimal:7',
            'start_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    // Helper methods
    public function getFormattedNumberAttribute(): string
    {
        return "{$this->number_type}: {$this->number_value}";
    }

    // Relationships
    public function customer()
    {
        return $this->morphTo();
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
