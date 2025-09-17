<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DefaultManual;
use App\Models\DefaultManualTranslation;
use App\Models\ManualSection;

class AdminDefaultManualController extends Controller
{
    public function index(Request $request)
    {
        $query = DefaultManual::with(['section', 'translations']);

        if ($request->filled('section_id')) {
            $query->where('manual_section_id', $request->get('section_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $manuals = $query->orderByDesc('id')->paginate(15);
        $sections = ManualSection::orderBy('sort_order')->get();

        return view('admin.default-manuals.index', compact('manuals', 'sections'));
    }

    public function create()
    {
        $sections = ManualSection::orderBy('sort_order')->get();
        return view('admin.default-manuals.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'manual_section_id' => 'required|exists:manual_sections,id',
            'pdf_path' => 'nullable|string|max:500',
            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
        ]);

        $manual = DefaultManual::create([
            'manual_section_id' => $validated['manual_section_id'],
            'title' => $request->input('title') ?? null, // not used when translations provided
            'content' => $request->input('content') ?? null,
            'pdf_path' => $validated['pdf_path'] ?? null,
        ]);

        foreach ($validated['translations'] as $tr) {
            $manual->translations()->create([
                'locale' => $tr['locale'],
                'title' => $tr['title'],
                'content' => $tr['content'],
            ]);
        }

        return redirect()->route('admin.default-manuals.show', $manual)
            ->with('success', 'Инструкция успешно создана');
    }

    public function show(DefaultManual $defaultManual)
    {
        $defaultManual->load(['section', 'translations']);
        return view('admin.default-manuals.show', compact('defaultManual'));
    }

    public function edit(DefaultManual $defaultManual)
    {
        $defaultManual->load('translations');
        $sections = ManualSection::orderBy('sort_order')->get();
        return view('admin.default-manuals.edit', compact('defaultManual', 'sections'));
    }

    public function update(Request $request, DefaultManual $defaultManual)
    {
        $validated = $request->validate([
            'manual_section_id' => 'required|exists:manual_sections,id',
            'pdf_path' => 'nullable|string|max:500',
            'translations' => 'required|array|min:1',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.content' => 'required|string',
        ]);

        $defaultManual->update([
            'manual_section_id' => $validated['manual_section_id'],
            'pdf_path' => $validated['pdf_path'] ?? null,
        ]);

        // Replace translations
        $defaultManual->translations()->delete();
        foreach ($validated['translations'] as $tr) {
            $defaultManual->translations()->create([
                'locale' => $tr['locale'],
                'title' => $tr['title'],
                'content' => $tr['content'],
            ]);
        }

        return redirect()->route('admin.default-manuals.show', $defaultManual)
            ->with('success', 'Инструкция успешно обновлена');
    }

    public function destroy(DefaultManual $defaultManual)
    {
        $defaultManual->delete();
        return redirect()->route('admin.default-manuals.index')
            ->with('success', 'Инструкция удалена');
    }
}


