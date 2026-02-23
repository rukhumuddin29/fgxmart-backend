<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'swift_iban',
        'is_active',
        'is_primary',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];
}
