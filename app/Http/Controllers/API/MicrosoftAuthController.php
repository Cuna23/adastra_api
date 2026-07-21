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

        // Domain check - sama pattern macam AuthController::login()
        if (!str_ends_with($email, '@adastra.com.my') && !str_ends_with($email, '@adastraip.com')) {
            return redirect(env('FRONTEND_URL') . '/login?error=invalid_domain');
        }

        // Cari user sedia ada
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Belum ada account - tolak dulu (safer default).
            // Kalau nak auto-create account instead, bagitau, boleh tukar logic ni.
            return redirect(env('FRONTEND_URL') . '/login?error=account_not_found');
        }

        // Generate Sanctum token - sama function macam login biasa
        $token = $user->createToken('auth-token')->plainTextToken;

        // Redirect balik ke Flutter dengan token dalam query param (Approach A)
        return redirect(env('FRONTEND_URL') . '/auth-success?token=' . $token);
    }
}