<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'color_code',
        'sorting_order'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class , 'product_attribute_values')
            ->withPivot(['price_adjustment', 'price', 'stock', 'sku', 'is_default'])
            ->withTimestamps();
    }
}
