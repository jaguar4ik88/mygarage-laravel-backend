<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReminderType;
use App\Models\ReminderTypeTranslation;
use Illuminate\Http\Request;

class AdminReminderTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ReminderType::with('translations');

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('key', 'like', "%{$search}%");
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('is_active', $status);
        }

        $reminderTypes = $query->latest()->paginate(15);

        return view('admin.reminder-types.index', compact('reminderTypes'));
    }

    public function show(ReminderType $reminderType)
    {
        $reminderType->load('translations');
        return view('admin.reminder-types.show', compact('reminderType'));
    }

    public function create()
    {
        return view('admin.reminder-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:reminder_types,key',
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
        ]);

        $reminderType = ReminderType::create([
            'key' => $request->key,
            'icon' => $request->icon,
            'color' => $request->color,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Создаем переводы
        foreach ($request->translations as $translation) {
            $reminderType->translations()->create([
                'locale' => $translation['locale'],
                'title' => $translation['title'],
            ]);
        }

        return redirect()->route('admin.reminder-types.show', $reminderType)
            ->with('success', 'Тип напоминания успешно создан');
    }

    public function edit(ReminderType $reminderType)
    {
        $reminderType->load('translations');
        return view('admin.reminder-types.edit', compact('reminderType'));
    }

    public function update(Request $request, ReminderType $reminderType)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:reminder_types,key,' . $reminderType->id,
            'icon' => 'required|string|max:255',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
        ]);

        $reminderType->update([
            'key' => $request->key,
            'icon' => $request->icon,
            'color' => $request->color,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        // Обновляем переводы
        $reminderType->translations()->delete();
        foreach ($request->translations as $translation) {
            $reminderType->translations()->create([
                'locale' => $translation['locale'],
                'title' => $translation['title'],
            ]);
        }

        return redirect()->route('admin.reminder-types.show', $reminderType)
            ->with('success', 'Тип напоминания успешно обновлен');
    }

    public function destroy(ReminderType $reminderType)
    {
        $reminderType->delete();

        return redirect()->route('admin.reminder-types.index')
            ->with('success', 'Тип напоминания успешно удален');
    }
}
