<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Make sure to import Str

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Validate incoming request data including password confirmation.
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:users,phone',
            'password'  => 'required|string|min:6|confirmed',
            'store_id'  => 'required|exists:stores,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the user
        $user = User::create([
            'full_name' => $request->full_name,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'store_id'  => $request->store_id,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user
        ], 201);
    }

    /**
     * Log in an existing user.
     */
    public function login(Request $request)
    {
        // Validate the login credentials.
        $credentials = $request->only('phone', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Set the user resolver on the request so that $request->user() returns the authenticated user.
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Manually create a token without using createToken:
        // 1. Generate a plain-text token.
        $plainTextToken = Str::random(40);
        // 2. Hash the token using SHA256 (Sanctum stores tokens hashed).
        $hashedToken = hash('sha256', $plainTextToken);

        // 3. Create a new token record using $request->user()->tokens()
        $request->user()->tokens()->create([
            'name'      => 'auth_token',
            'token'     => $hashedToken,
            'abilities' => ['*'], // Customize token abilities if needed
        ]);

        return response()->json([
            'message'      => 'User logged in successfully',
            'access_token' => $plainTextToken,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }
}
