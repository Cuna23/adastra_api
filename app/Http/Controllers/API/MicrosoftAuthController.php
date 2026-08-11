<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class MicrosoftAuthController extends Controller
{
    // Step 1: staff klik "Continue with Microsoft" -> pergi sini -> redirect ke Microsoft
    public function redirect()
    {
        return Socialite::driver('microsoft')->stateless()->redirect();
    }

    // Step 2: Microsoft redirect balik sini lepas staff login
    public function callback()
    {
        try {
            $msUser = Socialite::driver('microsoft')->stateless()->user();
        } catch (\Exception $e) {
            return redirect(env('FRONTEND_URL') . '/login?error=microsoft_auth_failed');
        }

        $email = $msUser->getEmail();

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $msUser->getName() ?? $email,
                'password' => Hash::make(Str::random(32)),
                'role' => 'staff',
                'status' => 'active',
            ]
        );

        $token = $user->createToken('auth-token')->plainTextToken;

        return redirect(env('FRONTEND_URL') . '/auth-success?token=' . $token);
    }
}