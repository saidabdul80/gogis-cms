<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lga extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    // Relationships
    public function individualCustomers(): HasMany
    {
        return $this->hasMany(IndividualCustomer::class);
    }
}
