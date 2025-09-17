<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;

class CarDataController extends Controller
{
    private string $carQueryBaseUrl = 'http://www.carqueryapi.com/api/0.3';

    public function makers(Request $request)
    {
        // Always fetch from remote (cached). If refresh=true, bypass cache.
        $refresh = filter_var($request->query('refresh'), FILTER_VALIDATE_BOOL) || filter_var($request->query('force'), FILTER_VALIDATE_BOOL);
        $debug = filter_var($request->query('debug'), FILTER_VALIDATE_BOOL);
        $cacheKey = 'carquery_makers_v1';
        if ($refresh) {
            Cache::forget($cacheKey);
        }
        $data = Cache::remember($cacheKey, now()->addDays(7), function () {
            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->retry(2, 500)
                ->get($this->carQueryBaseUrl.'/?cmd=getMakes&fmt=json');
            if (!$response->ok()) {
                abort(502, 'Failed to fetch makes');
            }
            $json = $response->json();
            $makers = collect($json['Makes'] ?? [])->pluck('make_display')->filter()->values()->all();
            foreach ($makers as $name) {
                CarMaker::firstOrCreate(['name' => $name]);
            }
            return $makers;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function models(Request $request)
    {
        $make = (string) $request->query('maker', '');
        $year = $request->query('year');
        if ($make === '') {
            return response()->json(['success' => false, 'message' => 'maker is required'], 422);
        }
        $maker = CarMaker::firstOrCreate(['name' => $make]);
        $makeParam = strtolower($make);
        $refresh = filter_var($request->query('refresh'), FILTER_VALIDATE_BOOL) || filter_var($request->query('force'), FILTER_VALIDATE_BOOL);
        $debug = filter_var($request->query('debug'), FILTER_VALIDATE_BOOL);
        $cacheKey = 'carquery_models_v1:'.md5($make.'|'.($year ?? ''));
        if ($refresh) {
            Cache::forget($cacheKey);
        }
        $debugInfo = [];
        $data = Cache::remember($cacheKey, now()->addDays(7), function () use ($makeParam, $year, $maker, &$debugInfo) {
            try {
                $variants = array_unique([$makeParam, ucfirst($makeParam), strtoupper($makeParam)]);
                $models = [];
                foreach ($variants as $variant) {
                    foreach ([true, false] as $soldInUs) {
                        if (!empty($models)) break 2;
                        $url = $this->carQueryBaseUrl.'/?cmd=getModels&make='.urlencode($variant).'&fmt=json'.($soldInUs ? '&sold_in_us=1' : '');
                        if (!empty($year)) {
                            $url .= '&year='.urlencode($year);
                        }
                        \Log::info('CarDataController@getModels URL', ['url' => $url]);
                        $debugInfo[] = $url;
                        $response = Http::withHeaders(['Accept' => 'application/json'])
                            ->retry(2, 500)
                            ->get($url);
                        if ($response->ok()) {
                            $body = $response->body();
                            $json = json_decode($body, true);
                            $payload = $json['Models'] ?? ($json['models'] ?? []);
                            $found = collect(is_array($payload) ? $payload : [])->pluck('model_name')->filter()->values()->all();
                            if (!empty($found)) { $models = $found; break; }
                            // Try raw decode strictly
                            $raw = json_decode($body, true);
                            $payload = $raw['Models'] ?? ($raw['models'] ?? []);
                            $found = collect(is_array($payload) ? $payload : [])->pluck('model_name')->filter()->values()->all();
                            if (!empty($found)) { $models = $found; break; }
                        }
                    }
                }

                if (empty($models)) {
                    foreach ($variants as $variant) {
                        foreach ([true, false] as $soldInUs) {
                            if (!empty($models)) break 2;
                            $url2 = $this->carQueryBaseUrl.'/?cmd=getTrims&make='.urlencode($variant).'&fmt=json'.($soldInUs ? '&sold_in_us=1' : '');
                            if (!empty($year)) {
                                $url2 .= '&year='.urlencode($year);
                            }
                            \Log::info('CarDataController@getModels Fallback URL', ['url' => $url2]);
                            $debugInfo[] = $url2;
                            $resp2 = Http::withHeaders(['Accept' => 'application/json'])
                                ->retry(2, 500)
                                ->get($url2);
                            if ($resp2->ok()) {
                                $body2 = $resp2->body();
                                $json2 = json_decode($body2, true);
                                $payload2 = $json2['Trims'] ?? ($json2['trims'] ?? []);
                                $found = collect(is_array($payload2) ? $payload2 : [])->pluck('model_name')->filter()->unique()->sort()->values()->all();
                                if (!empty($found)) { $models = $found; break; }
                                $raw2 = json_decode($body2, true);
                                $payload2 = $raw2['Trims'] ?? ($raw2['trims'] ?? []);
                                $found = collect(is_array($payload2) ? $payload2 : [])->pluck('model_name')->filter()->unique()->sort()->values()->all();
                                if (!empty($found)) { $models = $found; break; }
                            }
                        }
                    }
                }
                foreach ($models as $name) {
                    CarModel::firstOrCreate(['car_maker_id' => $maker->id, 'name' => $name]);
                }
                return $models;
            } catch (\Throwable $e) {
                return [];
            }
        });

        $responsePayload = [
            'success' => true,
            'data' => $data,
        ];
        if ($debug) {
            $responsePayload['debug'] = $debugInfo;
        }
        return response()->json($responsePayload);
    }

    public function trims(Request $request)
    {
        $make = (string) $request->query('maker', '');
        $model = (string) $request->query('model', '');
        $year = $request->query('year');
        if ($make === '' || $model === '') {
            return response()->json(['success' => false, 'message' => 'maker and model are required'], 422);
        }
        $maker = CarMaker::firstOrCreate(['name' => $make]);
        $modelRow = CarModel::firstOrCreate(['car_maker_id' => $maker->id, 'name' => $model]);
        $cacheKey = 'carquery_trims_v1:'.md5($make.'|'.$model.'|'.($year ?? ''));
        $data = Cache::remember($cacheKey, now()->addDays(7), function () use ($make, $model, $year, $maker, $modelRow) {
            try {
                $url = $this->carQueryBaseUrl.'/?cmd=getTrims&make='.urlencode($make).'&model='.urlencode($model).'&fmt=json';
                if (!empty($year)) {
                    $url .= '&year='.urlencode($year);
                }
                $response = Http::withHeaders(['Accept' => 'application/json'])
                    ->retry(2, 500)
                    ->get($url);
                if (!$response->ok()) {
                    return [];
                }
                $json = $response->json();
                $payload = $json['Trims'] ?? ($json['trims'] ?? []);
                $trims = collect($payload)->map(function ($t) {
                    $engine = $t['trim_engine'] ?? ($t['engine'] ?? '');
                    if (!$engine) {
                        $parts = [];
                        if (!empty($t['model_trim'])) { $parts[] = $t['model_trim']; }
                        if (!empty($t['model_engine_fuel'])) { $parts[] = $t['model_engine_fuel']; }
                        if (!empty($t['model_engine_l'])) { $parts[] = $t['model_engine_l'].'L'; }
                        if (!empty($t['model_engine_cc'])) { $parts[] = $t['model_engine_cc'].'cc'; }
                        if (!empty($t['model_engine_power_hp'])) { $parts[] = $t['model_engine_power_hp'].'hp'; }
                        $engine = implode(' · ', array_filter($parts));
                    }
                    if (!$engine) { $engine = 'Unknown'; }
                    return [
                        'engine' => $engine,
                        'raw' => $t,
                    ];
                })->values()->all();
                foreach ($trims as $t) {
                    CarEngine::firstOrCreate([
                        'car_maker_id' => $maker->id,
                        'car_model_id' => $modelRow->id,
                        'label' => $t['engine'],
                    ], [
                        'raw' => $t['raw'] ?? null,
                    ]);
                }
                return $trims;
            } catch (\Throwable $e) {
                return [];
            }
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}


