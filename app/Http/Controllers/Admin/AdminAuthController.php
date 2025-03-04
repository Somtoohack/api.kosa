<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\LogLoginJob;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if the admin email exists
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        // Check if the password matches
        if (!Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Create a token for the admin
        $token = $admin->createToken('AdminToken')->plainTextToken;
        $ip = $request->ip();
        if ($ip == '127.0.0.1') {
            $ip = '102.88.84.85';
        }
        dispatch(new LogLoginJob($admin, $request->email, $request->device_id, $request->device_name, $ip, $request->header('User-Agent')));

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token,
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:admins',
            'name' => 'required|max:20',
            'password' => 'required|min:6|confirmed',
        ],
            [
                'email.unique' => 'Email already exists',
                'password.confirmed' => 'Password does not match',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new admin
        $admin = Admin::create([
            'email' => $request->email,
            'name' => $request->name,
            'admin_key' => getTrx(5) . '-' . now()->timestamp,
            'password' => Hash::make($request->password),
        ]);

        // Create a token for the authenticated admin
        $token = $admin->createToken('AdminToken')->plainTextToken;
        $ip = $request->ip();
        if ($ip == '127.0.0.1') {
            $ip = '102.88.84.85';
        }
        dispatch(new LogLoginJob($admin, $request->email, $request->device_id, $request->device_name, $ip, $request->header('User-Agent')));
        return response()->json([
            'status' => true,
            'message' => 'Admin registered successfully',
            'business' => $admin,
            'token' => $token,
        ], 201);
    }
}