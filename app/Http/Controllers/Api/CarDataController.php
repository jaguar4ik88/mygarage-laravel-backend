<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;

class CarDataController extends Controller
{
    // External API disabled for runtime requests. Data should be prefilled via import command.

    public function makers(Request $request)
    {
        $refresh = filter_var($request->query('refresh'), FILTER_VALIDATE_BOOL) || filter_var($request->query('force'), FILTER_VALIDATE_BOOL);
        $cacheKey = 'car_data_makers_v1';
        if ($refresh) { Cache::forget($cacheKey); }
        $data = Cache::remember($cacheKey, now()->addDays(7), function () {
            return CarMaker::query()->orderBy('name')->pluck('name')->all();
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function models(Request $request)
    {
        $make = (string) $request->query('maker', '');
        if ($make === '') {
            return response()->json(['success' => false, 'message' => 'maker is required'], 422);
        }
        $maker = CarMaker::firstOrCreate(['name' => $make]);
        $refresh = filter_var($request->query('refresh'), FILTER_VALIDATE_BOOL) || filter_var($request->query('force'), FILTER_VALIDATE_BOOL);
        $cacheKey = 'car_data_models_v1:'.md5($make);
        if ($refresh) { Cache::forget($cacheKey); }
        $data = Cache::remember($cacheKey, now()->addDays(7), function () use ($maker) {
            return CarModel::query()
                ->where('car_maker_id', $maker->id)
                ->orderBy('name')
                ->pluck('name')
                ->all();
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function trims(Request $request)
    {
        $make = (string) $request->query('maker', '');
        $model = (string) $request->query('model', '');
        if ($make === '' || $model === '') {
            return response()->json(['success' => false, 'message' => 'maker and model are required'], 422);
        }
        $maker = CarMaker::firstOrCreate(['name' => $make]);
        $modelRow = CarModel::firstOrCreate(['car_maker_id' => $maker->id, 'name' => $model]);
        $cacheKey = 'car_data_trims_v1:'.md5($make.'|'.$model);
        $data = Cache::remember($cacheKey, now()->addDays(7), function () use ($maker, $modelRow) {
            return CarEngine::query()
                ->where('car_maker_id', $maker->id)
                ->where('car_model_id', $modelRow->id)
                ->orderBy('label')
                ->pluck('label')
                ->map(fn ($label) => ['engine' => $label])
                ->values()
                ->all();
        });

        return response()->json(['success' => true, 'data' => $data]);
    }
}


