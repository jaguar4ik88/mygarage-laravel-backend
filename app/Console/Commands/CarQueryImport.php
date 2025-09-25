<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;

class CarQueryImport extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'carquery:import {--maker=} {--year=} {--limit=0 : Limit number of makers to import (0 = all)}';

    /**
     * The console command description.
     */
    protected $description = 'Import car makers, models and trims from CarQuery API into local DB';

    private string $carQueryBaseUrl = 'http://www.carqueryapi.com/api/0.3';
    private string $carQueryBaseUrlHttps = 'https://www.carqueryapi.com/api/0.3';

    public function handle(): int
    {
        $onlyMaker = (string) $this->option('maker');
        $year = $this->option('year');
        $limit = (int) $this->option('limit');

        $makers = [];
        if ($onlyMaker !== '') {
            $makers = [$onlyMaker];
        } else {
            $this->info('Step 1/3: Fetching makers');
            // Use HTTPS for makers (HTTP often returns 403/empty)
            $resp = $this->http()->retry(3, 750)->get($this->carQueryBaseUrlHttps.'/?cmd=getMakes&fmt=json');
            if (!$resp->ok()) {
                $this->error('Failed to fetch makers');
                return self::FAILURE;
            }
            $json = $this->decodeFlexibleJson($resp->body());
            $payload = $json['Makes'] ?? ($json['makes'] ?? []);
            $makers = collect(is_array($payload) ? $payload : [])->pluck('make_display')->filter()->unique()->values()->all();
            if (empty($makers)) {
                Log::warning('CarQueryImport: empty makers payload', [
                    'status' => $resp->status(),
                    'body_sample' => substr($resp->body() ?? '', 0, 500),
                ]);
                // Fallback to a minimal built-in makers list to allow progress
                $makers = [
                    'Toyota','Honda','Nissan','Mazda','Mitsubishi','Subaru','Suzuki','Lexus','Acura','Infiniti',
                    'BMW','Mercedes-Benz','Audi','Volkswagen','Porsche','Opel','Ford','Chevrolet','Kia','Hyundai',
                    'Skoda','Seat','Peugeot','Citroën','Renault','Volvo','Jaguar','Land Rover','Fiat','Alfa Romeo'
                ];
            }
        }

        if ($limit > 0) {
            $makers = array_slice($makers, 0, $limit);
        }

        $totalMakers = count($makers);
        $this->info("Importing {$totalMakers} maker(s)...");

        foreach ($makers as $index => $makeName) {
            $this->line("[".($index+1)."/{$totalMakers}] Maker: {$makeName}");
            // Upsert maker
            $maker = CarMaker::updateOrCreate(['name' => $makeName], []);

            // Step 2/3: Models for maker
            $urlModels = $this->carQueryBaseUrl.'/?cmd=getModels&make='.urlencode($makeName).'&fmt=json';
            if (!empty($year)) { $urlModels .= '&year='.urlencode($year); }
            Log::info('CarQueryImport@getModels', ['url' => $urlModels]);
            $rModels = $this->http()->retry(2, 500)->get($urlModels);
            $models = [];
            if ($rModels->ok()) {
                $json = $this->decodeFlexibleJson($rModels->body());
                $payload = $json['Models'] ?? ($json['models'] ?? []);
                $models = collect(is_array($payload) ? $payload : [])->pluck('model_name')->filter()->unique()->values()->all();
            }
            if (empty($models)) {
                // Fallback: derive model list from trims
                $urlTrimsForModels = $this->carQueryBaseUrl.'/?cmd=getTrims&make='.urlencode($makeName).'&fmt=json';
                if (!empty($year)) { $urlTrimsForModels .= '&year='.urlencode($year); }
                Log::info('CarQueryImport@fallbackModels', ['url' => $urlTrimsForModels]);
                $rT4M = $this->http()->retry(2, 500)->get($urlTrimsForModels);
                if ($rT4M->ok()) {
                    $json = $this->decodeFlexibleJson($rT4M->body());
                    $payload = $json['Trims'] ?? ($json['trims'] ?? []);
                    $models = collect(is_array($payload) ? $payload : [])->pluck('model_name')->filter()->unique()->sort()->values()->all();
                }
            }

            foreach ($models as $modelName) {
                // Upsert model
                $model = CarModel::updateOrCreate(['car_maker_id' => $maker->id, 'name' => $modelName], []);

                // Step 3/3: Engines (trims) for model
                $urlTrims = $this->carQueryBaseUrl.'/?cmd=getTrims&make='.urlencode($makeName).'&model='.urlencode($modelName).'&fmt=json';
                if (!empty($year)) { $urlTrims .= '&year='.urlencode($year); }
                $rTrims = $this->http()->retry(2, 500)->get($urlTrims);
                $trims = [];
                if ($rTrims->ok()) {
                    $json = $this->decodeFlexibleJson($rTrims->body());
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
                }
                foreach ($trims as $t) {
                    CarEngine::updateOrCreate([
                        'car_maker_id' => $maker->id,
                        'car_model_id' => $model->id,
                        'label' => $t['engine'],
                    ], [
                        'raw' => $t['raw'] ?? null,
                    ]);
                }
                // polite delay per model
                usleep(150 * 1000);
            }

            // Small delay to be nice to the API
            usleep(200 * 1000);
        }

        $this->info('Import finished.');
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
        // Remove JSONP wrappers like callback( ... )
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


