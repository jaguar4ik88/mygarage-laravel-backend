<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdviceSection;
use App\Models\TranslationGroup;
use App\Models\Translation;
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
        ]);

        // Create title translation group
        $titleGroup = TranslationGroup::create();
        
        Translation::create([
            'translation_group_id' => $titleGroup->id,
            'locale' => 'en',
            'title' => $request->title_en,
        ]);
        
        Translation::create([
            'translation_group_id' => $titleGroup->id,
            'locale' => 'ru',
            'title' => $request->title_ru,
        ]);
        
        Translation::create([
            'translation_group_id' => $titleGroup->id,
            'locale' => 'uk',
            'title' => $request->title_uk,
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
        ]);

        // Update translations
        $titleGroup = $adviceSection->titleGroup;
        
        $titleGroup->translations()->where('locale', 'en')->update(['title' => $request->title_en]);
        $titleGroup->translations()->where('locale', 'ru')->update(['title' => $request->title_ru]);
        $titleGroup->translations()->where('locale', 'uk')->update(['title' => $request->title_uk]);

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
