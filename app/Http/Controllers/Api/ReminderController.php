<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        // Сначала деактивируем просроченные напоминания для текущего пользователя
        Reminder::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->where('next_service_date', '<', now())
            ->update(['is_active' => false]);

        // Получаем напоминания только текущего пользователя
        $reminders = Reminder::where('user_id', $request->user()->id)
            ->with('user')
            ->orderBy('is_active', 'desc') // Сначала активные (ожидание), потом неактивные (отработано)
            ->orderBy('next_service_date', 'asc') // Внутри каждой группы по дате
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reminders
        ]);
    }

    public function store(Request $request)
    {
        // Логируем входящие данные для отладки
        \Log::info('Creating reminder with data:', $request->all());
        
        $user = $request->user();
        
        // Reminder limits removed - users can delete reminders
        // No need to check subscription limits anymore
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'next_service_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Добавляем user_id из аутентифицированного пользователя
        $reminderData = $request->all();
        $reminderData['user_id'] = $user->id;
        
        $reminder = Reminder::create($reminderData);
        \Log::info('Reminder created successfully:', $reminder->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Reminder created successfully',
            'data' => $reminder
        ], 201);
    }

    public function show(Request $request, $id)
    {
        // For public access, find reminder by ID directly
        $reminder = Reminder::with('user')->findOrFail($id);
        
        // Обновляем статус активности если нужно
        if ($reminder->is_active && $reminder->next_service_date && $reminder->next_service_date->isPast()) {
            $reminder->is_active = false;
            $reminder->save();
        }

        return response()->json([
            'success' => true,
            'data' => $reminder
        ]);
    }

    public function update(Request $request, $id)
    {
        // Find reminder by ID and ensure it belongs to the authenticated user
        $reminder = Reminder::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'next_service_date' => 'sometimes|required|date',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $reminder->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Reminder updated successfully',
            'data' => $reminder
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // For public access, find reminder by ID directly
        $reminder = Reminder::findOrFail($id);

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder deleted successfully'
        ]);
    }

    public function byUser(Request $request, $userId)
    {
        // Сначала деактивируем просроченные напоминания для указанного пользователя
        Reminder::where('user_id', $userId)
            ->where('is_active', true)
            ->where('next_service_date', '<', now())
            ->update(['is_active' => false]);

        // For testing purposes, return all reminders regardless of user/vehicle
        $reminders = Reminder::where('user_id', $userId)
            ->with('user')
            ->orderBy('is_active', 'desc') // Сначала активные (ожидание), потом неактивные (отработано)
            ->orderBy('next_service_date', 'asc') // Внутри каждой группы по дате
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reminders
        ]);
    }
}
