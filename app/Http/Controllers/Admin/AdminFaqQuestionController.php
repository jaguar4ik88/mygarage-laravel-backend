<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqQuestion;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class AdminFaqQuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = FaqQuestion::with(['category', 'translations']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('key', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('faq_category_id', $request->get('category_id'));
        }

        if ($request->filled('status')) {
            $status = $request->get('status') === 'active';
            $query->where('is_active', $status);
        }

        $questions = $query->orderBy('sort_order')->paginate(15);
        $categories = FaqCategory::orderBy('sort_order')->get();

        return view('admin.faq.questions.index', compact('questions', 'categories'));
    }

    public function show(FaqQuestion $question)
    {
        $question->load(['category.translations', 'translations']);
        return view('admin.faq.questions.show', compact('question'));
    }

    public function create()
    {
        $categories = FaqCategory::orderBy('sort_order')->get();
        return view('admin.faq.questions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'key' => 'required|string|max:255|unique:faq_questions,key',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.question' => 'required|string|max:500',
            'translations.*.answer' => 'required|string',
        ]);

        $question = FaqQuestion::create([
            'faq_category_id' => $request->faq_category_id,
            'key' => $request->key,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        foreach ($request->translations as $translation) {
            $question->translations()->create([
                'locale' => $translation['locale'],
                'question' => $translation['question'],
                'answer' => $translation['answer'],
            ]);
        }

        return redirect()->route('admin.faq.questions.show', $question)
            ->with('success', 'Вопрос FAQ успешно создан');
    }

    public function edit(FaqQuestion $question)
    {
        $question->load('translations');
        $categories = FaqCategory::orderBy('sort_order')->get();
        return view('admin.faq.questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, FaqQuestion $question)
    {
        $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'key' => 'required|string|max:255|unique:faq_questions,key,' . $question->id,
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.question' => 'required|string|max:500',
            'translations.*.answer' => 'required|string',
        ]);

        $question->update([
            'faq_category_id' => $request->faq_category_id,
            'key' => $request->key,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        $question->translations()->delete();
        foreach ($request->translations as $translation) {
            $question->translations()->create([
                'locale' => $translation['locale'],
                'question' => $translation['question'],
                'answer' => $translation['answer'],
            ]);
        }

        return redirect()->route('admin.faq.questions.show', $question)
            ->with('success', 'Вопрос FAQ успешно обновлен');
    }

    public function destroy(FaqQuestion $question)
    {
        $question->delete();

        return redirect()->route('admin.faq.questions.index')
            ->with('success', 'Вопрос FAQ успешно удален');
    }
}
