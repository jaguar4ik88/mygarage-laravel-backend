<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminUserController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query();

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Фильтр по роли
        if ($request->filled('role')) {
            $role = $request->get('role');
            if ($role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($role === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'vehicles',
            'reminders',
            'serviceStations',
            'subscriptions' => function ($query) {
                $query->with('subscription')->latest();
            },
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Сброс подписки пользователя до Free (для тестирования в админке).
     * Не затрагивает магазин Apple/Google — только серверное состояние.
     */
    public function cancelSubscription(Request $request, User $user)
    {
        $admin = $request->user('admin');

        UserSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'cancelled_at' => now()]);

        $user->update([
            'plan_type' => 'free',
            'subscription_expires_at' => null,
            'platform' => null,
            'transaction_id' => null,
        ]);

        Log::info('Admin cancelled user subscription for testing', [
            'target_user_id' => $user->id,
            'admin_id' => $admin?->id,
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Подписка сброшена: пользователь переведён на план Free (только серверное состояние).');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'currency' => 'nullable|string|in:UAH,USD,EUR',
            'is_admin' => 'boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'currency' => $request->currency ?? 'UAH',
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Пользователь успешно создан');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'currency' => 'nullable|string|in:UAH,USD,EUR',
            'is_admin' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'currency' => $request->currency ?? 'UAH',
            'is_admin' => $request->boolean('is_admin'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Пользователь успешно обновлен');
    }

    public function destroy(User $user)
    {
        // Нельзя удалить самого себя
        if ($user->id === auth()->guard('admin')->id()) {
            return redirect()->back()
                ->with('error', 'Нельзя удалить самого себя');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно удален');
    }
}
