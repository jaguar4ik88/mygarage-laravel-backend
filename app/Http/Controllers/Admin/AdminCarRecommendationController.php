<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarRecommendation;
use App\Models\ManualSection;
use Illuminate\Http\Request;

class AdminCarRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $query = CarRecommendation::query();

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('maker', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('recommendation', 'like', "%{$search}%")
                  ->orWhereHas('manualSection', function($subQ) use ($search) {
                      $subQ->where('slug', 'like', "%{$search}%")
                           ->orWhere('key', 'like', "%{$search}%");
                  });
            });
        }

        // Фильтр по марке
        if ($request->filled('maker')) {
            $query->where('maker', $request->get('maker'));
        }

        // Фильтр по типу обслуживания
        if ($request->filled('manual_section_id')) {
            $query->where('manual_section_id', $request->get('manual_section_id'));
        }

        $recommendations = $query->with('manualSection')->latest()->paginate(15);

        // Получаем уникальные марки и секции для фильтров
        $makers = CarRecommendation::distinct()->pluck('maker')->sort()->values();
        $manualSections = ManualSection::orderBy('sort_order')->get();

        return view('admin.car-recommendations.index', compact('recommendations', 'makers', 'manualSections'));
    }

    public function show(CarRecommendation $carRecommendation)
    {
        $carRecommendation->load('manualSection');
        return view('admin.car-recommendations.show', compact('carRecommendation'));
    }

    public function create()
    {
        $manualSections = ManualSection::orderBy('sort_order')->get();
        return view('admin.car-recommendations.create', compact('manualSections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'maker' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage_interval' => 'required|integer|min:1',
            'recommendation' => 'required|string',
            'manual_section_id' => 'required|exists:manual_sections,id',
        ]);

        CarRecommendation::create($request->all());

        return redirect()->route('admin.car-recommendations.index')
            ->with('success', 'Рекомендация успешно создана');
    }

    public function edit(CarRecommendation $carRecommendation)
    {
        $manualSections = ManualSection::orderBy('sort_order')->get();
        return view('admin.car-recommendations.edit', compact('carRecommendation', 'manualSections'));
    }

    public function update(Request $request, CarRecommendation $carRecommendation)
    {
        $request->validate([
            'maker' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage_interval' => 'required|integer|min:1',
            'recommendation' => 'required|string',
            'manual_section_id' => 'required|exists:manual_sections,id',
        ]);

        $carRecommendation->update($request->all());

        return redirect()->route('admin.car-recommendations.index')
            ->with('success', 'Рекомендация успешно обновлена');
    }

    public function destroy(CarRecommendation $carRecommendation)
    {
        $carRecommendation->delete();

        return redirect()->route('admin.car-recommendations.index')
            ->with('success', 'Рекомендация успешно удалена');
    }
}