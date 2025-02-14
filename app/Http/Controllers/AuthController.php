<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user along with their store.
     */
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required|string|max:20|unique:users,phone',
            'store_name'     => 'required|string|max:255',
            'store_address'  => 'required|string|max:255',
            'password'       => 'required|string|min:6|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Create the store
        $store = Store::create([
            'store_name'    => $request->store_name,
            'store_address' => $request->store_address,
        ]);
    
        // Create the user
        $user = User::create([
            'full_name' => $request->full_name,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'store_id'  => $store->id,
        ]);
    
        // Generate token immediately after registration
        $plainTextToken = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message'      => 'User registered successfully',
            'user'         => $user,
            'store'        => $store,
            'access_token' => $plainTextToken,
            'token_type'   => 'Bearer',
        ], 201);
    }
    

    /**
     * Log in an existing user.
     */
    public function login(Request $request)
    {
        $credentials = $request->only('phone', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // $user = Auth::user();

        $plainTextToken = Str::random(40);
        $hashedToken = hash('sha256', $plainTextToken);

        $request->user()->tokens()->create([
            'name'      => 'auth_token',
            'token'     => $hashedToken,
            'abilities' => ['*'],
        ]);

        return response()->json([
            'message'      => 'User logged in successfully',
            'access_token' => $plainTextToken,
            'token_type'   => 'Bearer',
            'user'         => $request->user(),
        ]);
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'User logged out successfully']);
        }

        return response()->json(['message' => 'No authenticated user found'], 401);
    }
}
