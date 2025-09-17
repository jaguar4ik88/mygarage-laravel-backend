<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use App\Models\TranslationGroup;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenseTypes = ExpenseType::with('translationGroup.translations')->get();
        return view('admin.expense-types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.expense-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|unique:expense_types,slug',
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.title' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // Create translation group
            $group = TranslationGroup::create();
            
            // Create translations
            foreach ($request->translations as $translation) {
                Translation::create([
                    'translation_group_id' => $group->id,
                    'locale' => $translation['locale'],
                    'title' => $translation['title'],
                ]);
            }

            // Create expense type
            ExpenseType::create([
                'slug' => $request->slug,
                'is_active' => $request->boolean('is_active', true),
                'translation_group_id' => $group->id,
            ]);
        });

        return redirect()->route('admin.expense-types.index')
            ->with('success', 'Expense type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseType $expenseType)
    {
        $expenseType->load('translationGroup.translations');
        return view('admin.expense-types.show', compact('expenseType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseType $expenseType)
    {
        $expenseType->load('translationGroup.translations');
        return view('admin.expense-types.edit', compact('expenseType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate([
            'slug' => 'required|string|unique:expense_types,slug,' . $expenseType->id,
            'is_active' => 'boolean',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.title' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $expenseType) {
            // Update expense type
            $expenseType->update([
                'slug' => $request->slug,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update translations
            if ($expenseType->translationGroup) {
                $expenseType->translationGroup->translations()->delete();
            } else {
                $group = TranslationGroup::create();
                $expenseType->update(['translation_group_id' => $group->id]);
            }

            foreach ($request->translations as $translation) {
                Translation::create([
                    'translation_group_id' => $expenseType->translation_group_id,
                    'locale' => $translation['locale'],
                    'title' => $translation['title'],
                ]);
            }
        });

        return redirect()->route('admin.expense-types.index')
            ->with('success', 'Expense type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseType $expenseType)
    {
        DB::transaction(function () use ($expenseType) {
            // Delete translations
            if ($expenseType->translationGroup) {
                $expenseType->translationGroup->translations()->delete();
                $expenseType->translationGroup->delete();
            }
            
            // Delete expense type
            $expenseType->delete();
        });

        return redirect()->route('admin.expense-types.index')
            ->with('success', 'Expense type deleted successfully.');
    }
}
