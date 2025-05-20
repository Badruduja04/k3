<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApiAuthController extends Controller
{
    /**
     * Login user and create token
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'NUP' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find user by NUP and password
            $user = DB::table('users')
                ->where('NUP', $request->NUP)
                ->where('password', $request->password)
                ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'NUP atau password salah'
                ], 401);
            }

            // Create token
            $token = bin2hex(random_bytes(40));
            
            // Update user's api_token
            DB::table('users')
                ->where('id', $user->id)
                ->update(['api_token' => $token]);

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'NUP' => $user->NUP,
                        'departement' => $user->departement,
                        'sub_departement' => $user->sub_departement,
                        'status' => $user->status
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error in Login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user (Revoke the token)
     */
    public function logout(Request $request)
    {
        try {
            // Remove api_token
            DB::table('users')
                ->where('id', $request->user()->id)
                ->update(['api_token' => null]);
            
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error in Logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated User
     */
    public function user(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
        ]);
    }
} 