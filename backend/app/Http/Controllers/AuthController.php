<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $allowedEmails = [
                'yurikoaishinselo@uvers.ac.id',
                'asrulpuadi@gmail.com',
                'yongming.ja@gmail.com'
            ];

            if (!in_array(strtolower($googleUser->getEmail()), $allowedEmails)) {
                return redirect('/login?error=unauthorized');
            }

            $user = User::updateOrCreate(
                ['email' => strtolower($googleUser->getEmail())],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            // TODO: Here we should also check invitations and add to projects

            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect back to frontend with the token (now on the same domain)
            return redirect('/auth/callback?token=' . $token);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 500);
        }
    }

    public function getAllUsers()
    {
        return response()->json(User::select('id', 'name', 'avatar', 'email')->get());
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme_preference' => 'required|integer|in:1,2'
        ]);

        $user = $request->user();
        $user->theme_preference = $request->theme_preference;
        $user->save();

        return response()->json(['message' => 'Theme updated successfully', 'user' => $user]);
    }
}
