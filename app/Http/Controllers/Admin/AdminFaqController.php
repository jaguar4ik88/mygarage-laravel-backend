<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use App\Models\FaqCategoryTranslation;
use App\Models\FaqQuestionTranslation;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    // Управление категориями FAQ
    public function index(Request $request)
    {
        $query = FaqCategory::with('translations');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('key', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('is_active', $status);
        }

        $categories = $query->orderBy('sort_order')->paginate(15);

        return view('admin.faq.categories.index', compact('categories'));
    }

    public function show(FaqCategory $category)
    {
        $category->load(['translations', 'questions.translations']);
        return view('admin.faq.categories.show', compact('category'));
    }

    public function create()
    {
        return view('admin.faq.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:faq_categories,key',
            'icon' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $category = FaqCategory::create([
            'key' => $request->key,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        foreach ($request->translations as $translation) {
            $category->translations()->create([
                'locale' => $translation['locale'],
                'name' => $translation['name'],
            ]);
        }

        return redirect()->route('admin.faq.categories.show', $category)
            ->with('success', 'Категория FAQ успешно создана');
    }

    public function edit(FaqCategory $category)
    {
        $category->load('translations');
        return view('admin.faq.categories.edit', compact('category'));
    }

    public function update(Request $request, FaqCategory $category)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:faq_categories,key,' . $category->id,
            'icon' => 'required|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $category->update([
            'key' => $request->key,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        $category->translations()->delete();
        foreach ($request->translations as $translation) {
            $category->translations()->create([
                'locale' => $translation['locale'],
                'name' => $translation['name'],
            ]);
        }

        return redirect()->route('admin.faq.categories.show', $category)
            ->with('success', 'Категория FAQ успешно обновлена');
    }

    public function destroy(FaqCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.faq.categories.index')
            ->with('success', 'Категория FAQ успешно удалена');
    }

}
