<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualSection;
use App\Support\AdminTranslations;
use Illuminate\Http\Request;

class AdminManualSectionController extends Controller
{
    public function index(Request $request)
    {
        $query = ManualSection::with('translations');

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

        $manualSections = $query->orderBy('sort_order')->paginate(15);

        return view('admin.manual-sections.index', compact('manualSections'));
    }

    public function show(ManualSection $manualSection)
    {
        $manualSection->load('translations');
        return view('admin.manual-sections.show', compact('manualSection'));
    }

    public function create()
    {
        return view('admin.manual-sections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:manual_sections,key',
            'icon' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            ...AdminTranslations::textRules('title'),
        ]);

        $manualSection = ManualSection::create([
            'key' => $request->key,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        AdminTranslations::persist($manualSection->translations(), $request->translations, ['title']);

        return redirect()->route('admin.manual-sections.show', $manualSection)
            ->with('success', 'Секция мануала успешно создана');
    }

    public function edit(ManualSection $manualSection)
    {
        $manualSection->load('translations');
        return view('admin.manual-sections.edit', compact('manualSection'));
    }

    public function update(Request $request, ManualSection $manualSection)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:manual_sections,key,' . $manualSection->id,
            'icon' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            ...AdminTranslations::textRules('title'),
        ]);

        $manualSection->update([
            'key' => $request->key,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        AdminTranslations::persist($manualSection->translations(), $request->translations, ['title']);

        return redirect()->route('admin.manual-sections.show', $manualSection)
            ->with('success', 'Секция мануала успешно обновлена');
    }

    public function destroy(ManualSection $manualSection)
    {
        $manualSection->delete();

        return redirect()->route('admin.manual-sections.index')
            ->with('success', 'Секция мануала успешно удалена');
    }
}
