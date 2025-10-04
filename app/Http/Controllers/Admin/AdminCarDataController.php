<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;

class AdminCarDataController extends Controller
{
    public function index(Request $request)
    {
        return view('admin/car-data/index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return back()->with('error', 'Не удалось прочитать CSV');
        }

        $pos = ftell($handle);
        $line = fgets($handle);
        fseek($handle, $pos);
        $delimiter = ',';
        if ($line !== false) {
            $candidates = [',',';','\t'];
            $counts = [];
            foreach ($candidates as $d) { $counts[$d] = substr_count($line, $d); }
            arsort($counts);
            $delimiter = array_key_first($counts) ?: ',';
        }

        $header = null; $rowNum = 0; $created = 0; $updated = 0; $errors = [];
        $makerMap = CarMaker::query()->pluck('id','name')->all();
        $modelMap = [];
        $engineSet = [];

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNum++;
                if ($rowNum === 1) {
                    $normalized = array_map(function ($c) {
                        $v = trim(preg_replace('/\s+/', ' ', $c ?? ''));
                        $map = [
                            'марка' => 'maker','бренд' => 'maker','maker' => 'maker','make' => 'maker',
                            'модель' => 'model','model' => 'model',
                            'двигатель' => 'engine','engine' => 'engine','trim' => 'engine',
                        ];
                        return $map[mb_strtolower($v)] ?? $v;
                    }, $data);
                    $hasKnown = count(array_intersect(['maker','model','engine'], $normalized)) >= 2;
                    if ($hasKnown) { $header = $normalized; continue; }
                }

                // Extract columns
                if ($header) {
                    $idx = array_flip($header);
                    $makerName = $data[$idx['maker'] ?? 0] ?? '';
                    $modelName = $data[$idx['model'] ?? 1] ?? '';
                    $engineLabel = $data[$idx['engine'] ?? 2] ?? '';
                } else {
                    $makerName = $data[0] ?? '';
                    $modelName = $data[1] ?? '';
                    $engineLabel = $data[2] ?? '';
                }

                $makerName = trim(preg_replace('/\s+/', ' ', (string) $makerName));
                $modelName = trim(preg_replace('/\s+/', ' ', (string) $modelName));
                $engineLabel = trim(preg_replace('/\s+/', ' ', (string) $engineLabel));
                if ($makerName === '' || $modelName === '' || $engineLabel === '') { $errors[] = "Строка {$rowNum}: пустые значения"; continue; }

                if (!isset($makerMap[$makerName])) {
                    $maker = CarMaker::updateOrCreate(['name' => $makerName], []);
                    $makerMap[$makerName] = $maker->id; $created++;
                }
                $makerId = $makerMap[$makerName];

                $modelKey = $makerId.'|'.$modelName;
                if (!isset($modelMap[$modelKey])) {
                    $model = CarModel::updateOrCreate(['car_maker_id' => $makerId, 'name' => $modelName], []);
                    $modelMap[$modelKey] = $model->id; $created++;
                } else { $updated++; }
                $modelId = $modelMap[$modelKey];

                $engineKey = $makerId.'|'.$modelId.'|'.$engineLabel;
                if (!isset($engineSet[$engineKey])) {
                    CarEngine::updateOrCreate([
                        'car_maker_id' => $makerId,
                        'car_model_id' => $modelId,
                        'label' => $engineLabel,
                    ], []);
                    $engineSet[$engineKey] = true; $created++;
                } else { $updated++; }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Импорт не выполнен: '.$e->getMessage());
        }
        fclose($handle);

        return back()->with('success', "Импорт завершён. Создано: {$created}, Обновлено: {$updated}. Ошибок: ".count($errors));
    }
}


