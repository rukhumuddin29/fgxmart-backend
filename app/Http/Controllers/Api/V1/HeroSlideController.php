<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    /**
     * Display a listing of the slides for the frontend.
     */
    public function index()
    {
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $slides
        ]);
    }

    /**
     * Display a listing of all slides for the admin.
     */
    public function adminIndex()
    {
        $slides = HeroSlide::orderBy('sort_order')->get();

        return response()->json([
            'status' => 'success',
            'data' => $slides
        ]);
    }

    /**
     * Store a newly created slide.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'cta_text' => 'nullable|string|max:50',
            'cta_url' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'background_color' => 'nullable|string|max:20',
            'title_font_size' => 'nullable|string|max:20',
            'title_font_weight' => 'nullable|string|max:20',
            'title_color' => 'nullable|string|max:20',
            'title_font_family' => 'nullable|string|max:100',
            'subtitle_font_size' => 'nullable|string|max:20',
            'subtitle_font_weight' => 'nullable|string|max:20',
            'subtitle_color' => 'nullable|string|max:20',
            'subtitle_font_family' => 'nullable|string|max:100',
            'cta_bg_color' => 'nullable|string|max:20',
            'cta_text_color' => 'nullable|string|max:20',
            'cta_font_size' => 'nullable|string|max:20',
            'cta_font_weight' => 'nullable|string|max:20',
            'image_object_fit' => 'nullable|string|max:20',
            'image_object_position' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hero', 'public');
            $validated['image_path'] = $path;
        }

        $slide = HeroSlide::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Hero slide created successfully',
            'data' => $slide
        ], 201);
    }

    /**
     * Display the specified slide.
     */
    public function show(HeroSlide $heroSlide)
    {
        return response()->json([
            'status' => 'success',
            'data' => $heroSlide
        ]);
    }

    /**
     * Update the specified slide.
     */
    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'cta_text' => 'nullable|string|max:50',
            'cta_url' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'background_color' => 'nullable|string|max:20',
            'title_font_size' => 'nullable|string|max:20',
            'title_font_weight' => 'nullable|string|max:20',
            'title_color' => 'nullable|string|max:20',
            'title_font_family' => 'nullable|string|max:100',
            'subtitle_font_size' => 'nullable|string|max:20',
            'subtitle_font_weight' => 'nullable|string|max:20',
            'subtitle_color' => 'nullable|string|max:20',
            'subtitle_font_family' => 'nullable|string|max:100',
            'cta_bg_color' => 'nullable|string|max:20',
            'cta_text_color' => 'nullable|string|max:20',
            'cta_font_size' => 'nullable|string|max:20',
            'cta_font_weight' => 'nullable|string|max:20',
            'image_object_fit' => 'nullable|string|max:20',
            'image_object_position' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroSlide->image_path) {
                Storage::disk('public')->delete($heroSlide->image_path);
            }
            $path = $request->file('image')->store('hero', 'public');
            $validated['image_path'] = $path;
        }

        $heroSlide->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Hero slide updated successfully',
            'data' => $heroSlide
        ]);
    }

    /**
     * Remove the specified slide.
     */
    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->image_path) {
            Storage::disk('public')->delete($heroSlide->image_path);
        }
        
        $heroSlide->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Hero slide deleted successfully'
        ]);
    }
}
