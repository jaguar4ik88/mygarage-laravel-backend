<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdviceSection;
use App\Models\TranslationGroup;
use Illuminate\Http\Request;

class AdminAdviceSectionController extends Controller
{
    public function index()
    {
        $sections = AdviceSection::with(['titleGroup.translations'])
            ->orderBy('sort_order')
            ->get();

        return view('admin.advice-sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.advice-sections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:advice_sections',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_uk' => 'required|string|max:255',
            'title_pl' => 'nullable|string|max:255',
            'title_pt' => 'nullable|string|max:255',
            'title_ro' => 'nullable|string|max:255',
        ]);

        // Create title translation group
        $titleGroup = TranslationGroup::create();
        $titleGroup->syncColumn('title', [
            'en' => $request->title_en,
            'ru' => $request->title_ru,
            'uk' => $request->title_uk,
            'pl' => $request->title_pl,
            'pt' => $request->title_pt,
            'ro' => $request->title_ro,
        ]);

        AdviceSection::create([
            'slug' => $request->slug,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
            'title_translation_id' => $titleGroup->id,
        ]);

        return redirect()->route('admin.advice-sections.index')
            ->with('success', 'Advice section created successfully.');
    }

    public function show(AdviceSection $adviceSection)
    {
        $adviceSection->load(['titleGroup.translations', 'items.titleGroup.translations', 'items.contentGroup.translations']);
        return view('admin.advice-sections.show', compact('adviceSection'));
    }

    public function edit(AdviceSection $adviceSection)
    {
        $adviceSection->load('titleGroup.translations');
        return view('admin.advice-sections.edit', compact('adviceSection'));
    }

    public function update(Request $request, AdviceSection $adviceSection)
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:advice_sections,slug,' . $adviceSection->id,
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_uk' => 'required|string|max:255',
            'title_pl' => 'nullable|string|max:255',
            'title_pt' => 'nullable|string|max:255',
            'title_ro' => 'nullable|string|max:255',
        ]);

        // Update translations
        $adviceSection->titleGroup->syncColumn('title', [
            'en' => $request->title_en,
            'ru' => $request->title_ru,
            'uk' => $request->title_uk,
            'pl' => $request->title_pl,
            'pt' => $request->title_pt,
            'ro' => $request->title_ro,
        ]);

        $adviceSection->update([
            'slug' => $request->slug,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.advice-sections.index')
            ->with('success', 'Advice section updated successfully.');
    }

    public function destroy(AdviceSection $adviceSection)
    {
        // Delete translation groups
        if ($adviceSection->titleGroup) {
            $adviceSection->titleGroup->translations()->delete();
            $adviceSection->titleGroup->delete();
        }

        $adviceSection->delete();

        return redirect()->route('admin.advice-sections.index')
            ->with('success', 'Advice section deleted successfully.');
    }
}
