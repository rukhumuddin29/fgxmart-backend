<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
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
                'data' => Category::all()
            ]);
        }

        return response()->json(
            Category::orderBy('sorting_order', 'asc')->paginate($request->get('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'code' => 'nullable|string|max:10',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'sorting_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $upload = $this->imageService->upload($request->file('image'), 'categories');
            $imagePath = $upload['path'];
        }

        $category = Category::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'slug' => Str::slug($request->name),
            'image' => $imagePath,
            'sorting_order' => $request->get('sorting_order', 0),
            'status' => $request->get('status', true),
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'data' => $category
        ]);
    }

    public function showBySlug($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return response()->json([
            'data' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
            'code' => 'nullable|string|max:10',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            'sorting_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $this->imageService->delete($category->image);
            $upload = $this->imageService->upload($request->file('image'), 'categories');
            $category->image = $upload['path'];
        }

        $category->name = $request->name;
        $category->code = strtoupper($request->code);
        $category->slug = Str::slug($request->name);
        $category->sorting_order = $request->get('sorting_order', $category->sorting_order);
        $category->status = $request->get('status', $category->status);
        $category->save();

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        $this->imageService->delete($category->image);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
