<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'media', 'countries', 'attributeValues.attribute'])->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->has('min_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('discount_price', '>=', $request->min_price)
                    ->orWhere(function ($sq) use ($request) {
                    $sq->whereNull('discount_price')->where('base_price', '>=', $request->min_price);
                }
                );
            });
        }

        if ($request->has('max_price')) {
            $query->where(function ($q) use ($request) {
                $q->where('discount_price', '<=', $request->max_price)
                    ->orWhere(function ($sq) use ($request) {
                    $sq->whereNull('discount_price')->where('base_price', '<=', $request->max_price);
                }
                );
            });
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderByRaw('COALESCE(discount_price, base_price) ASC');
                    break;
                case 'price_desc':
                    $query->orderByRaw('COALESCE(discount_price, base_price) DESC');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
            }
        }

        return response()->json(
            $query->paginate($request->get('per_page', 12))
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string',
            'sku' => 'nullable|string|unique:products,sku',
            'variation_code' => 'nullable|string|max:20',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'base_price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'countries' => 'nullable|array',
            'countries.*' => 'exists:countries,id',
            'gallery' => 'nullable|array',
            'gallery.*' => 'mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',
            'attributes.*.price_adjustment' => 'nullable|numeric',
            'attributes.*.price' => 'nullable|numeric|min:0',
            'attributes.*.stock' => 'nullable|integer|min:0',
            'attributes.*.sku' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $coverPath = null;
            if ($request->hasFile('cover_image')) {
                $upload = $this->imageService->upload($request->file('cover_image'), 'products/covers');
                $coverPath = $upload['path'];
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . uniqid(),
                'sku' => $request->sku,
                'variation_code' => $request->variation_code,
                'summary' => $request->summary,
                'description' => $request->description,
                'cover_image' => $coverPath,
                'base_price' => $request->base_price,
                'discount_price' => $request->discount_price,
                'stock_quantity' => $request->stock_quantity,
                'status' => $request->get('status', true),
                'is_featured' => $request->get('is_featured', false),
            ]);

            if ($request->has('countries')) {
                $product->countries()->sync($request->countries);
            }

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $image) {
                    $upload = $this->imageService->upload($image, 'products/gallery');
                    $product->media()->create([
                        'file_path' => $upload['path'],
                        'sorting_order' => ($index + 1) * 10,
                        'is_primary' => false
                    ]);
                }
            }

            if ($request->has('attributes')) {
                $attributes = [];
                foreach ($request->attributes as $attr) {
                    $attributes[$attr['attribute_value_id']] = [
                        'price_adjustment' => $attr['price_adjustment'] ?? 0,
                        'price' => $attr['price'] ?? null,
                        'stock' => $attr['stock'] ?? 0,
                        'sku' => $attr['sku'] ?? null,
                    ];
                }
                $product->attributeValues()->sync($attributes);
            }


            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product->load(['category', 'brand', 'media', 'countries'])
            ], 201);
        });
    }

    public function show(Product $product)
    {
        return response()->json([
            'data' => $product->load(['category', 'brand', 'media', 'countries', 'attributeValues.attribute'])
        ]);
    }

    public function showBySlug($slug)
    {
        $product = Product::with(['category', 'brand', 'media', 'countries', 'attributeValues.attribute'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'data' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $attributes = null;
        // Hack for FormData processing in PUT request where PHP might fail to parse array structures properly
        if ($request->has('attributes') && is_array($request->attributes)) {
            $attributes = $request->attributes;
        }
        elseif ($request->has('attributes')) {
            $attributes = $request->input('attributes');
        }

        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'variation_code' => 'nullable|string|max:20',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'base_price' => 'numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'integer|min:0',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'countries' => 'nullable|array',
            'countries.*' => 'exists:countries,id',
            'attributes' => 'nullable|array',
            'attributes.*.attribute_value_id' => 'required|exists:attribute_values,id',
            'attributes.*.price_adjustment' => 'nullable|numeric',
            'attributes.*.price' => 'nullable|numeric|min:0',
            'attributes.*.stock' => 'nullable|integer|min:0',
            'attributes.*.sku' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $product, $attributes) {
            if ($request->hasFile('cover_image')) {
                $this->imageService->delete($product->cover_image);
                $upload = $this->imageService->upload($request->file('cover_image'), 'products/covers');
                $product->cover_image = $upload['path'];
            }

            $product->update([
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . $product->id,
                'sku' => $request->sku,
                'variation_code' => $request->variation_code,
                'summary' => $request->summary,
                'description' => $request->description,
                'base_price' => $request->base_price,
                'discount_price' => $request->discount_price,
                'stock_quantity' => $request->stock_quantity,
                'status' => $request->get('status', $product->status),
                'is_featured' => $request->get('is_featured', $product->is_featured),
            ]);

            if ($request->has('countries')) {
                $product->countries()->sync($request->countries);
            }

            if ($attributes !== null) {
                $syncAttributes = [];
                foreach ($attributes as $attr) {
                    $syncAttributes[$attr['attribute_value_id']] = [
                        'price_adjustment' => $attr['price_adjustment'] ?? 0,
                        'price' => $attr['price'] ?? null,
                        'stock' => $attr['stock'] ?? 0,
                        'sku' => $attr['sku'] ?? null,
                    ];
                }
                $product->attributeValues()->sync($syncAttributes);
            }


            if ($request->hasFile('gallery')) {
                $lastOrder = $product->media()->max('sorting_order') ?? 0;
                foreach ($request->file('gallery') as $index => $image) {
                    $upload = $this->imageService->upload($image, 'products/gallery');
                    $product->media()->create([
                        'file_path' => $upload['path'],
                        'sorting_order' => $lastOrder + ($index + 1) * 10,
                        'is_primary' => false
                    ]);
                }
            }

            if ($request->has('gallery_meta')) {
                $meta = json_decode($request->get('gallery_meta'), true);
                foreach ($meta as $item) {
                    if ($item['delete']) {
                        $media = ProductMedia::find($item['id']);
                        if ($media && $media->product_id === $product->id) {
                            $this->imageService->delete($media->file_path);
                            $media->delete();
                        }
                    }
                    else {
                        ProductMedia::where('id', $item['id'])
                            ->where('product_id', $product->id)
                            ->update(['sorting_order' => $item['order']]);
                    }
                }
            }

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product->load(['category', 'media', 'countries', 'attributeValues.attribute'])
            ]);
        });
    }

    public function destroy(Product $product)
    {
        return DB::transaction(function () use ($product) {
            $this->imageService->delete($product->cover_image);

            foreach ($product->media as $media) {
                $this->imageService->delete($media->file_path);
                $media->delete();
            }

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully'
            ]);
        });
    }

    public function generateSku(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'variation' => 'nullable|string'
        ]);

        $category = \App\Models\Category::find($request->category_id);
        $catCode = $category->code ?: strtoupper(substr($category->name, 0, 3));

        // Product part: get first 3-5 letters (consonants preferred or just first 3)
        $prodName = preg_replace('/[^a-zA-Z0-9]/', '', $request->name);
        $prodCode = strtoupper(substr($prodName, 0, 3));

        $varCode = $request->get('variation') ?: '000';

        // Sequence: find last product in this category
        $lastProduct = Product::where('category_id', $category->id)->orderBy('id', 'desc')->first();
        $seq = $lastProduct ? ($lastProduct->id + 1) : 1;
        $seqCode = str_pad($seq, 3, '0', STR_PAD_LEFT);

        $sku = "{$catCode}-{$prodCode}-{$varCode}-{$seqCode}";

        return response()->json(['sku' => $sku]);
    }
}
