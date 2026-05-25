<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // 2. Check email domain ← LETAK SINI
        if (!str_ends_with($request->email, '@adastra.com.my') && !str_ends_with($request->email, '@adastraip.com')) {
            return response()->json([
                'success' => false,
                'message' => 'Use Microsoft account @adastra.com.my/@adastraip.com'
            ], 403);
        }

        // 3. Find user
        $user = User::where('email', $request->email)->first();

        // 4. Check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // 5. Generate token
        $token = $user->createToken('auth-token')->plainTextToken;

        // 6. Return response
        return response()->json([
            'success' => true,
            'token'   => $token,
            'role'    => $user->role,
            'user'    => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user()
        ]);
    }
}