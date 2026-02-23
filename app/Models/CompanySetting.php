<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'name',
        'intro',
        'description',
        'logo_path',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zipcode',
        'country_code',
        'contact_email',
        'phone_number',
    ];
}
