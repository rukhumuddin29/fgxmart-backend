<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->with('country')->get();
        return response()->json(['data' => $addresses]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'type' => 'required|string|in:Home,Office,Other',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'zipcode' => 'required|string|max:20',
            'is_default' => 'boolean'
        ]);

        return DB::transaction(function () use ($request) {
            $user = $request->user();

            if ($request->is_default) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => false]);
            }

            // If it's the first address, make it default
            $isDefault = $request->is_default ?: ($user->addresses()->count() === 0);

            $address = $user->addresses()->create(array_merge(
                $request->all(),
            ['is_default' => $isDefault]
            ));

            return response()->json([
                'message' => 'Address added successfully',
                'data' => $address->load('country')
            ], 201);
        });
    }

    public function update(Request $request, UserAddress $address)
    {
        $this->authorizeAddress($request->user(), $address);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'type' => 'required|string|in:Home,Office,Other',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country_id' => 'required|exists:countries,id',
            'zipcode' => 'required|string|max:20',
            'is_default' => 'boolean'
        ]);

        return DB::transaction(function () use ($request, $address) {
            if ($request->is_default && !$address->is_default) {
                UserAddress::where('user_id', $request->user()->id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->all());

            return response()->json([
                'message' => 'Address updated successfully',
                'data' => $address->load('country')
            ]);
        });
    }

    public function destroy(Request $request, UserAddress $address)
    {
        $this->authorizeAddress($request->user(), $address);

        $address->delete();

        // If we deleted the default address, make another one default if available
        if ($address->is_default) {
            $nextAddress = UserAddress::where('user_id', $request->user()->id)->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        return response()->json(['message' => 'Address deleted successfully']);
    }

    public function setDefault(Request $request, UserAddress $address)
    {
        $this->authorizeAddress($request->user(), $address);

        DB::transaction(function () use ($request, $address) {
            UserAddress::where('user_id', $request->user()->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return response()->json(['message' => 'Default address updated']);
    }

    protected function authorizeAddress($user, $address)
    {
        if ($address->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
