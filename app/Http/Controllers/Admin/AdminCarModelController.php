<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarMaker;
use App\Models\CarModel;
use Illuminate\Http\Request;

class AdminCarModelController extends Controller
{
    public function index(Request $request)
    {
        $makerId = $request->integer('maker_id');
        $query = CarModel::query()->with('maker');
        if ($makerId) { $query->where('car_maker_id', $makerId); }
        $models = $query->orderBy('name')->paginate(25);
        $makers = CarMaker::orderBy('name')->get();
        return view('admin.car-data.models.index', compact('models','makers','makerId'));
    }

    public function create()
    {
        $makers = CarMaker::orderBy('name')->get();
        return view('admin.car-data.models.create', compact('makers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'car_maker_id' => 'required|integer|exists:car_makers,id',
            'name' => 'required|string|max:255',
        ]);
        CarModel::updateOrCreate(['car_maker_id' => $data['car_maker_id'], 'name' => trim($data['name'])], []);
        return redirect()->route('admin.car-data.models.index')->with('success', 'Модель сохранена');
    }

    public function edit(CarModel $model)
    {
        $makers = CarMaker::orderBy('name')->get();
        return view('admin.car-data.models.edit', compact('model','makers'));
    }

    public function update(Request $request, CarModel $model)
    {
        $data = $request->validate([
            'car_maker_id' => 'required|integer|exists:car_makers,id',
            'name' => 'required|string|max:255',
        ]);
        $model->update(['car_maker_id' => $data['car_maker_id'], 'name' => trim($data['name'])]);
        return redirect()->route('admin.car-data.models.index')->with('success', 'Модель обновлена');
    }

    public function destroy(CarModel $model)
    {
        $model->delete();
        return redirect()->route('admin.car-data.models.index')->with('success', 'Модель удалена');
    }
}


