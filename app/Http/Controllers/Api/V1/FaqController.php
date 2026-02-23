<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{


    public function index()
    {
        $faqs = Faq::orderBy('sort_order', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $faqs
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $faq = Faq::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ created successfully',
            'data' => $faq
        ]);
    }

    public function show(Faq $faq)
    {
        return response()->json([
            'status' => 'success',
            'data' => $faq
        ]);
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $faq->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQ updated successfully',
            'data' => $faq
        ]);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'FAQ deleted successfully'
        ]);
    }
}
