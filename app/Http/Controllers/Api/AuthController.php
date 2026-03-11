<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthController
 *
 * Manages the authentication lifecycle for the API.
 * Uses Laravel Sanctum to issue and revoke personal access tokens,
 * providing a secure way for users to interact with protected endpoints.
 */
class AuthController extends Controller
{
    /**
     * Authenticate a user and generate a new Bearer token.
     *
     * Validates the provided credentials against the users table.
     * Upon success, returns a plain-text token that must be used 
     * in the Authorization header for subsequent requests.
     *
     * @param Request $request Contains 'email' and 'password'.
     * @return JsonResponse JSON containing the access token and basic user info.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'As credenciais fornecidas estão incorretas.'
            ], 401);
        }

        /** * Create a new personal access token for the authenticated user.
         * The 'auth_token' string is a label for the token record.
         */
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'name' => $user->name,
                'role' => $user->role
            ]
        ]);
    }

    /**
     * Revoke the current access token being used by the user.
     *
     * This endpoint effectively signs the user out by deleting 
     * the token from the personal_access_tokens table.
     *
     * @param Request $request
     * @return JsonResponse Success message confirmation.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ]);
    }
}