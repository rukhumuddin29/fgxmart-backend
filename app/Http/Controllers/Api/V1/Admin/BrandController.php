<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        if ($request->has('all')) {
            return response()->json([
                'data' => Brand::all()
            ]);
        }

        return response()->json(
            Brand::orderBy('name', 'asc')->paginate($request->get('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:brands,name',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'status' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $upload = $this->imageService->upload($request->file('image'), 'brands');
            $imagePath = $upload['path'];
        }

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'brand_image' => $imagePath,
            'status' => $request->get('status', true),
        ]);

        return response()->json([
            'message' => 'Brand created successfully',
            'data' => $brand
        ], 201);
    }

    public function show(Brand $brand)
    {
        return response()->json([
            'data' => $brand
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|unique:brands,name,' . $brand->id,
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $this->imageService->delete($brand->brand_image);
            $upload = $this->imageService->upload($request->file('image'), 'brands');
            $brand->brand_image = $upload['path'];
        }

        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $brand->status = $request->get('status', $brand->status);
        $brand->save();

        return response()->json([
            'message' => 'Brand updated successfully',
            'data' => $brand
        ]);
    }

    public function destroy(Brand $brand)
    {
        if ($brand->brand_image) {
            $this->imageService->delete($brand->brand_image);
        }
        $brand->delete();

        return response()->json([
            'message' => 'Brand deleted successfully'
        ]);
    }
}
