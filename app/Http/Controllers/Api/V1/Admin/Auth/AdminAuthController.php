<?php

namespace App\Http\Controllers\Api\V1\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(AdminLoginRequest $request)
    {
        $data = $this->authService->adminLogin($request->email, $request->password);

        return response()->json([
            'message' => 'Admin Login Successful',
            'data' => $data,
        ]);
    }
}
