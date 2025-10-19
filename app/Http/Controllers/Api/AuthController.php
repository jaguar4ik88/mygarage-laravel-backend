<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        // Return authenticated user
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function googleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if Google API client is configured
            $clientId = config('services.google.client_id');
            
            if (!$clientId) {
                // Skip verification in development if not configured
                \Log::warning('Google Client ID not configured, skipping token verification');
                
                // For development: extract user info from unverified token
                // DO NOT USE IN PRODUCTION
                $parts = explode('.', $request->id_token);
                if (count($parts) !== 3) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid token format'
                    ], 401);
                }
                
                $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
                $googleId = $payload['sub'] ?? null;
                $email = $payload['email'] ?? null;
                $name = $payload['name'] ?? 'Google User';
            } else {
                // Verify Google ID Token
                $client = new \Google_Client(['client_id' => $clientId]);
                $payload = $client->verifyIdToken($request->id_token);
                
                if (!$payload) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid Google token'
                    ], 401);
                }

                // Verify that the token is for our app
                if ($payload['aud'] !== $clientId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid audience'
                    ], 401);
                }

                $googleId = $payload['sub'];
                $email = $payload['email'];
                $name = $payload['name'] ?? 'Google User';
            }

            if (!$googleId || !$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required user information'
                ], 401);
            }

            // Find or create user
            $user = User::where('google_id', $googleId)->first();
            
            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $email)->first();
                
                if ($user) {
                    // Link Google account to existing user
                    $user->update(['google_id' => $googleId]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'google_id' => $googleId,
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Google authentication successful',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Google auth error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Google authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function appleAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity_token' => 'required|string',
            'user' => 'nullable|string', // Данные пользователя (только при первой авторизации)
            'full_name' => 'nullable|string', // Полное имя (только при первой авторизации)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // For development: extract user info from unverified token
            // In production, you should verify the JWT signature with Apple's public keys
            $parts = explode('.', $request->identity_token);
            if (count($parts) !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token format'
                ], 401);
            }
            
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
            
            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Apple token'
                ], 401);
            }

            // Extract user info from token
            $appleId = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;
            
            if (!$appleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing Apple user ID'
                ], 401);
            }

            // Apple may not always provide email (privacy feature)
            // Use a placeholder email if not provided
            if (!$email) {
                $email = 'apple_' . $appleId . '@privaterelay.appleid.com';
            }

            // Получаем имя пользователя
            // Apple передаёт имя только при первой авторизации
            $userName = 'Apple User';
            
            if ($request->full_name) {
                // Если передано полное имя напрямую
                $userName = $request->full_name;
            } elseif ($request->user) {
                // Если передан JSON с данными пользователя
                $userData = json_decode($request->user, true);
                if ($userData && isset($userData['name'])) {
                    $fullName = $userData['name'];
                    // Комбинируем firstName и lastName
                    $userName = trim(
                        ($fullName['firstName'] ?? '') . ' ' . ($fullName['lastName'] ?? '')
                    );
                    if (empty($userName)) {
                        $userName = 'Apple User';
                    }
                }
            }

            // Find or create user
            $user = User::where('apple_id', $appleId)->first();
            
            if (!$user) {
                // Check if user exists with this email
                $user = User::where('email', $email)->first();
                
                if ($user) {
                    // Link Apple account to existing user
                    $user->update(['apple_id' => $appleId]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $userName,
                        'email' => $email,
                        'apple_id' => $appleId,
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Apple authentication successful',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Apple auth error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Apple authentication failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
