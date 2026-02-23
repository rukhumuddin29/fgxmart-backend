<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = Page::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $pages
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|array',
            'position' => 'nullable|string|in:header,footer,both,none',
            'status' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page = Page::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Page created successfully',
            'data' => $page
        ]);
    }

    public function show(Page $page)
    {
        return response()->json([
            'status' => 'success',
            'data' => $page
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|array',
            'position' => 'nullable|string|in:header,footer,both,none',
            'status' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        $page->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Page updated successfully',
            'data' => $page
        ]);
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Page deleted successfully'
        ]);
    }

    public function showBySlug($slug)
    {
        $page = Page::where('slug', $slug)->where('status', true)->firstOrFail();
        return response()->json([
            'status' => 'success',
            'data' => $page
        ]);
    }
}
