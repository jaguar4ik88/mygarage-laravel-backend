<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarMaker;
use Illuminate\Http\Request;

class AdminCarMakerController extends Controller
{
    public function index()
    {
        $makers = CarMaker::query()->orderBy('name')->paginate(25);
        return view('admin.car-data.makers.index', compact('makers'));
    }

    public function create()
    {
        return view('admin.car-data.makers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        CarMaker::updateOrCreate(['name' => trim($data['name'])], []);
        return redirect()->route('admin.car-data.makers.index')->with('success', 'Производитель сохранён');
    }

    public function edit(CarMaker $maker)
    {
        return view('admin.car-data.makers.edit', compact('maker'));
    }

    public function update(Request $request, CarMaker $maker)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $maker->update(['name' => trim($data['name'])]);
        return redirect()->route('admin.car-data.makers.index')->with('success', 'Производитель обновлён');
    }

    public function destroy(CarMaker $maker)
    {
        $maker->delete();
        return redirect()->route('admin.car-data.makers.index')->with('success', 'Производитель удалён');
    }
}


