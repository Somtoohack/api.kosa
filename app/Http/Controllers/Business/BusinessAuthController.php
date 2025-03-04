<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Jobs\LogLoginJob;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BusinessAuthController extends Controller
{

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $business = Business::where('email', $request->email)->first();

            if (!$business) {
                return response()->json([
                    'status' => false,
                    'message' => 'Business not found',
                ], 404);
            }

            if (!Hash::check($request->password, $business->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid password',
                ], 401);
            }

            $token = $business->createToken('BusinessToken')->plainTextToken;
            $ip = $request->ip();
            if ($ip == '127.0.0.1') {
                $ip = '102.88.84.85';
            }
            dispatch(new LogLoginJob($business, $request->email, $request->device_id, $request->device_name, $ip, $request->header('User-Agent')));

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'business' => $business,
                'token' => $token,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:businesses',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $business = Business::create([
                'name' => $request->name,
                'email' => $request->email,
                'business_key' => getTrx(5) . '-' . now()->timestamp,
                'password' => Hash::make($request->password),
            ]);

            $token = $business->createToken('BusinessToken')->plainTextToken;
            $ip = $request->ip();
            if ($ip == '127.0.0.1') {
                $ip = '102.88.84.85';
            }
            dispatch(new LogLoginJob($business, $request->email, $request->device_id ?? '', $request->device_name ?? '', $ip, $request->header('User-Agent')));

            return response()->json([
                'status' => true,
                'message' => 'Business registered successfully',
                'business' => $business,
                'token' => $token,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}