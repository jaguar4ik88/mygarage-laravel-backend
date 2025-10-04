<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;

class CarQueryEnginesImport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'carquery:engines {--year=} {--limit=0 : Limit number of makers to process (0 = all)} {--model-limit=0 : Limit models per maker}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch engines (trims) from CarQuery for our makers/models and upsert into DB';

    private string $carQueryBaseUrl = 'http://www.carqueryapi.com/api/0.3';

    public function handle(): int
    {
        $year = $this->option('year');
        $limit = (int) $this->option('limit');
        $modelLimit = (int) $this->option('model-limit');

        $makers = CarMaker::query()->orderBy('name')->pluck('name')->values();
        if ($limit > 0) { $makers = $makers->take($limit)->values(); }
        $totalMakers = $makers->count();
        $this->info("Processing {$totalMakers} maker(s) for engines via CarQuery…");

        $mi = 0; $created = 0; $updated = 0;
        foreach ($makers as $makerName) {
            $mi++;
            $this->line("[{$mi}/{$totalMakers}] Maker: {$makerName}");
            $maker = CarMaker::firstOrCreate(['name' => $makerName], []);

            $modelsQuery = CarModel::query()->where('car_maker_id', $maker->id)->orderBy('name');
            if ($modelLimit > 0) { $models = $modelsQuery->limit($modelLimit)->get(); } else { $models = $modelsQuery->get(); }
            $totalModels = $models->count();
            $mj = 0;
            foreach ($models as $model) {
                $mj++;
                $urlTrims = $this->carQueryBaseUrl.'/?cmd=getTrims&make='.urlencode($makerName).'&model='.urlencode($model->name).'&fmt=json';
                if (!empty($year)) { $urlTrims .= '&year='.urlencode($year); }
                try {
                    $resp = $this->http()->retry(2, 500)->get($urlTrims);
                } catch (\Illuminate\Http\Client\RequestException $e) {
                    $code = method_exists($e, 'response') && $e->response() ? $e->response()->status() : 0;
                    Log::warning('CarQueryEnginesImport: HTTP exception', [
                        'maker' => $makerName,
                        'model' => $model->name,
                        'status' => $code,
                        'message' => $e->getMessage(),
                        'url' => $urlTrims,
                    ]);
                    $this->warn("  - [{$mj}/{$totalModels}] {$model->name}: HTTP exception (".$code.") — skipping");
                    continue;
                } catch (\Throwable $e) {
                    Log::error('CarQueryEnginesImport: unexpected exception', [
                        'maker' => $makerName,
                        'model' => $model->name,
                        'message' => $e->getMessage(),
                        'url' => $urlTrims,
                    ]);
                    $this->warn("  - [{$mj}/{$totalModels}] {$model->name}: unexpected error — skipping");
                    continue;
                }
                if (!$resp->ok()) {
                    $this->warn("  - [{$mj}/{$totalModels}] {$model->name}: request failed (".$resp->status().")");
                    continue;
                }
                $json = $this->decodeFlexibleJson($resp->body());
                $payload = $json['Trims'] ?? ($json['trims'] ?? []);
                if (empty($payload)) {
                    Log::info('CarQueryEnginesImport: empty trims', ['maker' => $makerName, 'model' => $model->name]);
                    continue;
                }
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
                    $record = CarEngine::updateOrCreate([
                        'car_maker_id' => $maker->id,
                        'car_model_id' => $model->id,
                        'label' => $t['engine'],
                    ], [
                        'raw' => $t['raw'] ?? null,
                    ]);
                    if ($record->wasRecentlyCreated) { $created++; } else { $updated++; }
                }
                // small delay to be polite
                usleep(120 * 1000);
            }
            // delay per maker
            usleep(180 * 1000);
        }

        $this->info("Engines import finished. Created: {$created}, Updated: {$updated}.");
        return self::SUCCESS;
    }

    private function http()
    {
        return Http::withHeaders([
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            'Referer' => 'http://www.carqueryapi.com/',
        ]);
    }

    private function decodeFlexibleJson(?string $body): array
    {
        if ($body === null) { return []; }
        $trimmed = trim($body);
        if (str_contains($trimmed, '(') && str_contains($trimmed, ')') && !str_starts_with($trimmed, '{')) {
            $firstBrace = strpos($trimmed, '{');
            $lastBrace = strrpos($trimmed, '}');
            if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
                $trimmed = substr($trimmed, $firstBrace, $lastBrace - $firstBrace + 1);
            }
        }
        $json = json_decode($trimmed, true);
        return is_array($json) ? $json : [];
    }
}


