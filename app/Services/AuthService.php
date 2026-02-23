<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate an admin user and return a token.
     */
    public function adminLogin(string $email, string $password): array
    {
        $admin = Admin::where('email', $email)->first();

        if (!$admin || !Hash::check($password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        return [
            'token' => $admin->createToken('admin-token')->plainTextToken,
            'admin' => $admin,
        ];
    }
}
