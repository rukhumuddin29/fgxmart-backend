<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'position',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'content' => 'array',
        'status' => 'boolean',
    ];
}
