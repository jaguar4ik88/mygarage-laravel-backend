<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Генерируем 6-значный код
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Сохраняем код в таблице password_reset_tokens
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        // Отправляем письмо с кодом
        try {
            \Mail::send('emails.password-reset-code', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Код для сброса пароля - myGarage');
            });

            return response()->json([
                'success' => true,
                'message' => 'Password reset code sent to your email',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to send reset code',
            ], 500);
        }
    }

    /**
     * Handle reset password request
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:6',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Проверяем код
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset code',
            ], 400);
        }

        // Проверяем срок действия (60 минут)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            \DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Reset code has expired',
            ], 400);
        }

        // Проверяем код
        if (!Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset code',
            ], 400);
        }

        // Обновляем пароль
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Удаляем использованный код
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        event(new PasswordReset($user));

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully',
        ]);
    }

    /**
     * Get user-friendly error message for password reset status
     */
    private function getResetErrorMessage($status): string
    {
        return match ($status) {
            Password::INVALID_TOKEN => 'Invalid or expired reset token',
            Password::INVALID_USER => 'User not found',
            Password::THROTTLED => 'Too many attempts. Please try again later',
            default => 'Unable to reset password',
        };
    }
}

