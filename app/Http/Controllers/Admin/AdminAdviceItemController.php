<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdviceItem;
use App\Models\AdviceSection;
use App\Models\TranslationGroup;
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
            'title_pl' => 'nullable|string|max:255',
            'title_pt' => 'nullable|string|max:255',
            'title_ro' => 'nullable|string|max:255',
            'content_en' => 'required|string',
            'content_ru' => 'required|string',
            'content_uk' => 'required|string',
            'content_pl' => 'nullable|string',
            'content_pt' => 'nullable|string',
            'content_ro' => 'nullable|string',
        ]);

        $titleGroup = TranslationGroup::create();
        $titleGroup->syncColumn('title', [
            'en' => $request->title_en,
            'ru' => $request->title_ru,
            'uk' => $request->title_uk,
            'pl' => $request->title_pl,
            'pt' => $request->title_pt,
            'ro' => $request->title_ro,
        ]);

        $contentGroup = TranslationGroup::create();
        $contentGroup->syncColumn('content', [
            'en' => $request->content_en,
            'ru' => $request->content_ru,
            'uk' => $request->content_uk,
            'pl' => $request->content_pl,
            'pt' => $request->content_pt,
            'ro' => $request->content_ro,
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
            'title_pl' => 'nullable|string|max:255',
            'title_pt' => 'nullable|string|max:255',
            'title_ro' => 'nullable|string|max:255',
            'content_en' => 'required|string',
            'content_ru' => 'required|string',
            'content_uk' => 'required|string',
            'content_pl' => 'nullable|string',
            'content_pt' => 'nullable|string',
            'content_ro' => 'nullable|string',
        ]);

        $adviceItem->titleGroup->syncColumn('title', [
            'en' => $request->title_en,
            'ru' => $request->title_ru,
            'uk' => $request->title_uk,
            'pl' => $request->title_pl,
            'pt' => $request->title_pt,
            'ro' => $request->title_ro,
        ]);

        $adviceItem->contentGroup->syncColumn('content', [
            'en' => $request->content_en,
            'ru' => $request->content_ru,
            'uk' => $request->content_uk,
            'pl' => $request->content_pl,
            'pt' => $request->content_pt,
            'ro' => $request->content_ro,
        ]);

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
