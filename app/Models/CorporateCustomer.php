<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'registration_number',
        'registered_date',
        'email',
        'phone_number',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'registered_date' => 'date',
        ];
    }

    // Relationships
    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'customer');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'customer');
    }

    public function properties()
    {
        return $this->morphMany(Property::class, 'customer');
    }
}
