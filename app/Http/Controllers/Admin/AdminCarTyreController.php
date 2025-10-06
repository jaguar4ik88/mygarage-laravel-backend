<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarTyre;
use Illuminate\Http\Request;

class AdminCarTyreController extends Controller
{
    public function index(Request $request)
    {
        $query = CarTyre::query();

        // Поиск
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('dimension', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Фильтр по марке
        if ($request->filled('brand')) {
            $query->where('brand', $request->get('brand'));
        }

        // Фильтр по размеру
        if ($request->filled('dimension')) {
            $query->where('dimension', $request->get('dimension'));
        }

        $tyres = $query->latest()->paginate(15);

        // Получаем уникальные марки и размеры для фильтров
        $brands = CarTyre::distinct()->pluck('brand')->sort()->values();
        $dimensions = CarTyre::distinct()->pluck('dimension')->sort()->values();

        return view('admin.car-tyres.index', compact('tyres', 'brands', 'dimensions'));
    }

    public function show(CarTyre $carTyre)
    {
        return view('admin.car-tyres.show', compact('carTyre'));
    }

    public function create()
    {
        return view('admin.car-tyres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'dimension' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        CarTyre::create($request->all());

        return redirect()->route('admin.car-tyres.index')
            ->with('success', 'Рекомендация по шинам успешно создана');
    }

    public function edit(CarTyre $carTyre)
    {
        return view('admin.car-tyres.edit', compact('carTyre'));
    }

    public function update(Request $request, CarTyre $carTyre)
    {
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'dimension' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $carTyre->update($request->all());

        return redirect()->route('admin.car-tyres.index')
            ->with('success', 'Рекомендация по шинам успешно обновлена');
    }

    public function destroy(CarTyre $carTyre)
    {
        $carTyre->delete();

        return redirect()->route('admin.car-tyres.index')
            ->with('success', 'Рекомендация по шинам успешно удалена');
    }
}