<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $accounts
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'swift_iban' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
            BankAccount::where('is_primary', true)->update(['is_primary' => false]);
        }

        $account = BankAccount::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account created successfully',
            'data' => $account
        ]);
    }

    public function show(BankAccount $bankAccount)
    {
        return response()->json([
            'status' => 'success',
            'data' => $bankAccount
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'swift_iban' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary && !$bankAccount->is_primary) {
            BankAccount::where('is_primary', true)->update(['is_primary' => false]);
        }

        $bankAccount->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account updated successfully',
            'data' => $bankAccount
        ]);
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bank account deleted successfully'
        ]);
    }
}
