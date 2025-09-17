<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdviceItem;
use App\Models\AdviceSection;
use App\Models\TranslationGroup;
use App\Models\Translation;
use Illuminate\Http\Request;

class AdminAdviceItemController extends Controller
{
    public function index()
    {
        $items = AdviceItem::with(['section.titleGroup.translations', 'titleGroup.translations', 'contentGroup.translations'])
            ->orderBy('advice_section_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.advice-items.index', compact('items'));
    }

    public function create()
    {
        $sections = AdviceSection::with('titleGroup.translations')->get();
        return view('admin.advice-items.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'advice_section_id' => 'required|exists:advice_sections,id',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_uk' => 'required|string|max:255',
            'content_en' => 'required|string',
            'content_ru' => 'required|string',
            'content_uk' => 'required|string',
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

        // Create content translation group
        $contentGroup = TranslationGroup::create();
        
        Translation::create([
            'translation_group_id' => $contentGroup->id,
            'locale' => 'en',
            'content' => $request->content_en,
        ]);
        
        Translation::create([
            'translation_group_id' => $contentGroup->id,
            'locale' => 'ru',
            'content' => $request->content_ru,
        ]);
        
        Translation::create([
            'translation_group_id' => $contentGroup->id,
            'locale' => 'uk',
            'content' => $request->content_uk,
        ]);

        AdviceItem::create([
            'advice_section_id' => $request->advice_section_id,
            'title_translation_id' => $titleGroup->id,
            'content_translation_id' => $contentGroup->id,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.advice-items.index')
            ->with('success', 'Advice item created successfully.');
    }

    public function show(AdviceItem $adviceItem)
    {
        $adviceItem->load(['section.titleGroup.translations', 'titleGroup.translations', 'contentGroup.translations']);
        return view('admin.advice-items.show', compact('adviceItem'));
    }

    public function edit(AdviceItem $adviceItem)
    {
        $sections = AdviceSection::with('titleGroup.translations')->get();
        $adviceItem->load(['titleGroup.translations', 'contentGroup.translations']);
        return view('admin.advice-items.edit', compact('adviceItem', 'sections'));
    }

    public function update(Request $request, AdviceItem $adviceItem)
    {
        $request->validate([
            'advice_section_id' => 'required|exists:advice_sections,id',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'title_uk' => 'required|string|max:255',
            'content_en' => 'required|string',
            'content_ru' => 'required|string',
            'content_uk' => 'required|string',
        ]);

        // Update title translations
        $titleGroup = $adviceItem->titleGroup;
        $titleGroup->translations()->where('locale', 'en')->update(['title' => $request->title_en]);
        $titleGroup->translations()->where('locale', 'ru')->update(['title' => $request->title_ru]);
        $titleGroup->translations()->where('locale', 'uk')->update(['title' => $request->title_uk]);

        // Update content translations
        $contentGroup = $adviceItem->contentGroup;
        $contentGroup->translations()->where('locale', 'en')->update(['content' => $request->content_en]);
        $contentGroup->translations()->where('locale', 'ru')->update(['content' => $request->content_ru]);
        $contentGroup->translations()->where('locale', 'uk')->update(['content' => $request->content_uk]);

        $adviceItem->update([
            'advice_section_id' => $request->advice_section_id,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.advice-items.index')
            ->with('success', 'Advice item updated successfully.');
    }

    public function destroy(AdviceItem $adviceItem)
    {
        // Delete translation groups
        if ($adviceItem->titleGroup) {
            $adviceItem->titleGroup->translations()->delete();
            $adviceItem->titleGroup->delete();
        }

        if ($adviceItem->contentGroup) {
            $adviceItem->contentGroup->translations()->delete();
            $adviceItem->contentGroup->delete();
        }

        $adviceItem->delete();

        return redirect()->route('admin.advice-items.index')
            ->with('success', 'Advice item deleted successfully.');
    }
}
