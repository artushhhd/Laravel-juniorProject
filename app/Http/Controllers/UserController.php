<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data'    => [
                'user'  => $user,
                'access_token' => $user->createToken('auth')->plainTextToken,
                'token_type'   => 'Bearer'
            ]
        ], 201);
    }
    public function login(LoginRequest $request)
{
    $data = $request->validated();

    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.'
        ], 422);
    }
    if (!(bool)$user->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Your account has been blocked by an administrator.'
        ], 403);
    }
    return response()->json([
        'success' => true,
        'message' => 'User logged in successfully',
        'data'    => [
            'user'         => $user,
            'access_token' => $user->createToken('auth')->plainTextToken,
            'token_type'   => 'Bearer'
        ]
    ]);
}
public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'user' => $request->user()
            ]
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully'
        ]);
    }
}
