<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $upload = $this->imageService->upload($request->file('image'), 'editor', 1200, null, false);
            $url = Storage::disk('public')->url($upload['path']);

            return response()->json([
                'status' => 'success',
                'url' => $url
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No image provided'
        ], 400);
    }
}
