<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class CompanyInfoController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::first();
        $primaryBank = BankAccount::where('is_primary', true)->where('is_active', true)->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'company' => $settings,
                'bank' => $primaryBank
            ]
        ]);
    }
}
