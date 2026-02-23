<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function show()
    {
        $settings = CompanySetting::first();
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'intro' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'country_code' => 'required|string|size:2',
            'contact_email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $settings = CompanySetting::firstOrNew();
        $data = $request->only([
            'name', 'intro', 'description', 'address_line_1', 'address_line_2',
            'city', 'state', 'zipcode', 'country_code', 'contact_email', 'phone_number'
        ]);

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('company', 'public');
        }

        $settings->fill($data);
        $settings->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Company settings updated successfully',
            'data' => $settings
        ]);
    }
}
