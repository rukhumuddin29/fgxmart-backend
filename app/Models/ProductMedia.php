<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'file_path',
        'sorting_order',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileThumbnailUrlAttribute()
    {
        return asset('storage/' . \App\Services\ImageService::getThumbnailPath($this->file_path));
    }

    protected $appends = ['file_url', 'file_thumbnail_url'];
}
