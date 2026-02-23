<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'cta_text',
        'cta_url',
        'image_path',
        'background_color',
        'title_font_size',
        'title_font_weight',
        'title_color',
        'title_font_family',
        'subtitle_font_size',
        'subtitle_font_weight',
        'subtitle_color',
        'subtitle_font_family',
        'cta_bg_color',
        'cta_text_color',
        'cta_font_size',
        'cta_font_weight',
        'image_object_fit',
        'image_object_position',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
