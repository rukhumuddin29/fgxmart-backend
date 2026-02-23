<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'variation_code',
        'summary',
        'description',
        'cover_image',
        'base_price',
        'discount_price',
        'stock_quantity',
        'status',
        'is_featured'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sorting_order', 'asc');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class , 'product_country');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class , 'product_attribute_values')
            ->withPivot(['price_adjustment', 'price', 'stock', 'sku', 'is_default'])
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getCoverThumbnailUrlAttribute()
    {
        if (!$this->cover_image)
            return null;
        return asset('storage/' . \App\Services\ImageService::getThumbnailPath($this->cover_image));
    }

    protected $appends = ['cover_image_url', 'cover_thumbnail_url'];
}
