<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attribute::with('values')->orderBy('sorting_order', 'asc');

        if ($request->has('all')) {
            return response()->json([
                'data' => $query->get()
            ]);
        }

        return response()->json(
            $query->paginate($request->get('per_page', 10))
        );
    }

    /**
     * Store a newly created attribute in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:attributes,name',
            'sorting_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'values' => 'nullable|array',
            'values.*.value' => 'required|string',
            'values.*.color_code' => 'nullable|string',
            'values.*.sorting_order' => 'nullable|integer',
        ]);

        $attribute = Attribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sorting_order' => $request->get('sorting_order', 0),
            'status' => $request->get('status', true),
        ]);

        if ($request->has('values')) {
            foreach ($request->values as $val) {
                $attribute->values()->create([
                    'value' => $val['value'],
                    'slug' => Str::slug($val['value']),
                    'color_code' => $val['color_code'] ?? null,
                    'sorting_order' => $val['sorting_order'] ?? 0,
                ]);
            }
        }

        return response()->json([
            'message' => 'Attribute created successfully',
            'data' => $attribute->load('values')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        return response()->json([
            'data' => $attribute->load('values')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|unique:attributes,name,' . $attribute->id,
            'sorting_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'values' => 'nullable|array',
            'values.*.id' => 'nullable|integer|exists:attribute_values,id',
            'values.*.value' => 'required|string',
            'values.*.color_code' => 'nullable|string',
            'values.*.sorting_order' => 'nullable|integer',
        ]);

        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sorting_order' => $request->get('sorting_order', $attribute->sorting_order),
            'status' => $request->get('status', $attribute->status),
        ]);

        if ($request->has('values')) {
            $existingIds = collect($request->values)->pluck('id')->filter()->toArray();
            $attribute->values()->whereNotIn('id', $existingIds)->delete();

            foreach ($request->values as $val) {
                if (isset($val['id'])) {
                    AttributeValue::where('id', $val['id'])->update([
                        'value' => $val['value'],
                        'slug' => Str::slug($val['value']),
                        'color_code' => $val['color_code'] ?? null,
                        'sorting_order' => $val['sorting_order'] ?? 0,
                    ]);
                }
                else {
                    $attribute->values()->create([
                        'value' => $val['value'],
                        'slug' => Str::slug($val['value']),
                        'color_code' => $val['color_code'] ?? null,
                        'sorting_order' => $val['sorting_order'] ?? 0,
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Attribute updated successfully',
            'data' => $attribute->load('values')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return response()->json([
            'message' => 'Attribute deleted successfully'
        ]);
    }
}
