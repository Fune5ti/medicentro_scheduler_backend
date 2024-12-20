<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'required|string|max:255',
            'nif' => 'required|string|unique:patients,nif',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $patient = Patient::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'nif' => $request->nif,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'user_id' => $user->id,
        ]);

        // Assign the 'user' role
        $userRole = Role::where('name', 'user')->first();
        $user->roles()->attach($userRole);

        return response()->json(['message' => 'User registered successfully', 'user' => $user, 'patient' => $patient], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        $patient = Patient::where(['user_id' => $user->id])->first() ?? null;
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'patient' => $patient
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the user's token
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }
}
