<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required',
          	'company'=>'required',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'category' => 'client',
            'password' => Hash::make($request->password),
        ]);

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return response with success message
        return response()->json([
            'message' => 'User registered successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }


    /**
     * Login a user.
     */
public function login(Request $request)
{
    // Validate login details
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Check credentials
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'The provided credentials are incorrect.',
        ], 401);
    }

    // Get the authenticated user
    $user = Auth::user();

    // Create token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return response with success message
    return response()->json([
        'message' => 'Login successful.',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,  // Return user details if needed
    ], 200);
}


    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
