<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PrivacyPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $policies = PrivacyPolicy::with([])
            ->orderBy('language')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('language');

        return view('admin.privacy-policy.index', compact('policies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $languages = ['ru' => 'Русский', 'uk' => 'Українська', 'en' => 'English'];
        $sections = [
            'dataCollection' => 'Сбор данных',
            'dataUsage' => 'Использование данных', 
            'dataSharing' => 'Передача данных',
            'dataSecurity' => 'Безопасность данных',
            'userRights' => 'Права пользователя',
            'contact' => 'Контакты',
            'changes' => 'Изменения политики',
        ];

        return view('admin.privacy-policy.create', compact('languages', 'sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'language' => 'required|string|in:ru,uk,en',
            'section' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        PrivacyPolicy::create([
            'language' => $request->language,
            'section' => $request->section,
            'title' => $request->title,
            'content' => $request->content,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.privacy-policy.index')
            ->with('success', 'Раздел политики конфиденциальности создан успешно.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PrivacyPolicy $privacyPolicy): View
    {
        return view('admin.privacy-policy.show', compact('privacyPolicy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrivacyPolicy $privacyPolicy): View
    {
        $languages = ['ru' => 'Русский', 'uk' => 'Українська', 'en' => 'English'];
        $sections = [
            'dataCollection' => 'Сбор данных',
            'dataUsage' => 'Использование данных',
            'dataSharing' => 'Передача данных', 
            'dataSecurity' => 'Безопасность данных',
            'userRights' => 'Права пользователя',
            'contact' => 'Контакты',
            'changes' => 'Изменения политики',
        ];

        return view('admin.privacy-policy.edit', compact('privacyPolicy', 'languages', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrivacyPolicy $privacyPolicy): RedirectResponse
    {
        $request->validate([
            'language' => 'required|string|in:ru,uk,en',
            'section' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $privacyPolicy->update([
            'language' => $request->language,
            'section' => $request->section,
            'title' => $request->title,
            'content' => $request->content,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.privacy-policy.index')
            ->with('success', 'Раздел политики конфиденциальности обновлен успешно.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrivacyPolicy $privacyPolicy): RedirectResponse
    {
        $privacyPolicy->delete();

        return redirect()->route('admin.privacy-policy.index')
            ->with('success', 'Раздел политики конфиденциальности удален успешно.');
    }
}