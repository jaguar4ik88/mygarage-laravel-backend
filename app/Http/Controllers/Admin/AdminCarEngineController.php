<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;
use Illuminate\Http\Request;

class AdminCarEngineController extends Controller
{
    public function index(Request $request)
    {
        $makerId = $request->integer('maker_id');
        $modelId = $request->integer('model_id');
        $query = CarEngine::query()->with(['maker','model']);
        if ($makerId) { $query->where('car_maker_id', $makerId); }
        if ($modelId) { $query->where('car_model_id', $modelId); }
        $engines = $query->orderBy('label')->paginate(25);
        $makers = CarMaker::orderBy('name')->get();
        $models = $modelId ? CarModel::where('car_maker_id', $makerId)->orderBy('name')->get() : collect();
        return view('admin.car-data.engines.index', compact('engines','makers','models','makerId','modelId'));
    }

    public function create()
    {
        $makers = CarMaker::orderBy('name')->get();
        $models = collect();
        return view('admin.car-data.engines.create', compact('makers','models'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'car_maker_id' => 'required|integer|exists:car_makers,id',
            'car_model_id' => 'required|integer|exists:car_models,id',
            'label' => 'required|string|max:255',
        ]);
        CarEngine::updateOrCreate([
            'car_maker_id' => $data['car_maker_id'],
            'car_model_id' => $data['car_model_id'],
            'label' => trim($data['label']),
        ], []);
        return redirect()->route('admin.car-data.engines.index')->with('success', 'Двигатель сохранён');
    }

    public function edit(CarEngine $engine)
    {
        $makers = CarMaker::orderBy('name')->get();
        $models = CarModel::where('car_maker_id', $engine->car_maker_id)->orderBy('name')->get();
        return view('admin.car-data.engines.edit', compact('engine','makers','models'));
    }

    public function update(Request $request, CarEngine $engine)
    {
        $data = $request->validate([
            'car_maker_id' => 'required|integer|exists:car_makers,id',
            'car_model_id' => 'required|integer|exists:car_models,id',
            'label' => 'required|string|max:255',
        ]);
        $engine->update([
            'car_maker_id' => $data['car_maker_id'],
            'car_model_id' => $data['car_model_id'],
            'label' => trim($data['label']),
        ]);
        return redirect()->route('admin.car-data.engines.index')->with('success', 'Двигатель обновлён');
    }

    public function destroy(CarEngine $engine)
    {
        $engine->delete();
        return redirect()->route('admin.car-data.engines.index')->with('success', 'Двигатель удалён');
    }
}


