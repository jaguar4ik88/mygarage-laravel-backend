<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\CarMaker;
use App\Models\CarModel;
use App\Models\CarEngine;

class VpicImport extends Command
{
    protected $signature = 'vpic:import {--models} {--engine} {--limit=0 : Limit makers when importing models/engines}';

    protected $description = 'Import car data from NHTSA vPIC API: makes (default), models (--models), engines (--engine)';

    private string $vpicBase = 'https://vpic.nhtsa.dot.gov/api/vehicles';

    public function handle(): int
    {
        $doModels = (bool) $this->option('models');
        $doEngine = (bool) $this->option('engine');
        $limit = (int) $this->option('limit');

        if (!$doModels && !$doEngine) {
            return $this->importMakes();
        }

        if ($doModels) {
            return $this->importModels($limit);
        }

        if ($doEngine) {
            return $this->importEngines($limit);
        }

        return self::SUCCESS;
    }

    private function importMakes(): int
    {
        $this->info('Importing makes from vPIC…');
        $url = $this->vpicBase.'/GetMakesForVehicleType/car?format=json';
        $resp = $this->http()->get($url);
        if (!$resp->ok()) {
            $this->error('vPIC makes request failed: '.$resp->status());
            return self::FAILURE;
        }
        $json = $resp->json();
        $results = $json['Results'] ?? [];
        $names = collect($results)->pluck('MakeName')->filter()->unique()->values();
        $count = 0;
        foreach ($names as $name) {
            CarMaker::updateOrCreate(['name' => trim($name)], []);
            $count++;
        }
        $this->info("Imported/updated {$count} makers.");
        return self::SUCCESS;
    }

    private function importModels(int $limit): int
    {
        // Use makers from our DB as requested; filter out invalid names (e.g., with digits)
        $dbNames = CarMaker::query()->orderBy('name')->pluck('name')->map(fn ($n) => trim((string) $n));
        $invalid = [];
        $makers = $dbNames->filter(function ($name) use (&$invalid) {
            $ok = $this->isLikelyValidMakerName($name);
            if (!$ok) { $invalid[] = $name; }
            return $ok;
        })->unique()->values();
        if (!empty($invalid)) {
            Log::warning('vPIC models: skipped invalid maker names from DB', ['skipped' => array_values($invalid)]);
        }
        if ($limit > 0) { $makers = $makers->take($limit)->values(); }
        $total = $makers->count();
        $this->info("Importing models for {$total} maker(s) from vPIC using DB makers…");
        $i = 0; $created = 0;
        foreach ($makers as $makerName) {
            $i++;
            $slug = $this->makeVpicSlug($makerName);
            if ($slug === '') {
                $this->warn("[{$i}/{$total}] {$makerName}: skipped due to empty slug after normalization");
                continue;
            }
            if ($slug !== $this->makeVpicSlug((string) $makerName)) {
                // no-op check kept for clarity; normalization already done above
            }
            $url = $this->vpicBase.'/getmodelsformake/'.$slug.'?format=json';
            $resp = $this->http()->get($url);
            if (!$resp->ok()) {
                $this->warn("[{$i}/{$total}] {$makerName}: request failed (".$resp->status().")");
                continue;
            }
            $json = $resp->json();
            $rows = $json['Results'] ?? [];
            if (empty($rows)) {
                Log::info('vPIC models empty', ['maker' => $makerName]);
                continue;
            }
            $maker = CarMaker::firstOrCreate(['name' => $makerName]);
            $modelNames = collect($rows)->pluck('Model_Name')->filter()->unique()->values();
            foreach ($modelNames as $modelName) {
                CarModel::updateOrCreate(['car_maker_id' => $maker->id, 'name' => trim($modelName)], []);
                $created++;
            }
            $this->line("[{$i}/{$total}] {$makerName}: ".count($modelNames)." models");
            usleep(150 * 1000);
        }
        $this->info("Models import completed. Upserts: {$created}.");
        return self::SUCCESS;
    }

    private function isLikelyValidMakerName(string $name): bool
    {
        $name = trim($name);
        if ($name === '') { return false; }
        // Reject names containing any digits
        if (preg_match('/\p{N}/u', $name)) { return false; }
        // Allow letters (including unicode), marks, spaces, hyphen, ampersand, apostrophe, dot
        return (bool) preg_match("/^[\p{L}\p{M}][\p{L}\p{M}\s\-&.'’]+$/u", $name);
    }

    private function makeVpicSlug(string $name): string
    {
        $n = trim($name);
        if ($n === '') { return ''; }
        if (function_exists('mb_strtolower')) {
            $n = mb_strtolower($n, 'UTF-8');
        } else {
            $n = strtolower($n);
        }
        // Replace spaces and dashes with underscore
        $n = preg_replace('/[\s\-]+/u', '_', $n);
        // Replace any non [a-z0-9_] with underscore
        $n = preg_replace('/[^a-z0-9_]/u', '_', $n);
        // Collapse multiple underscores
        $n = preg_replace('/_+/u', '_', $n);
        // Trim leading/trailing underscores
        $n = trim($n, '_');
        return $n;
    }

    private function importEngines(int $limit): int
    {
        // vPIC does not provide a canonical engine list per model endpoint.
        // As a pragmatic approach, create a placeholder engine per (maker, model) if none exists yet.
        $query = CarModel::query()->with('maker');
        if ($limit > 0) {
            $query->limit($limit);
        }
        $models = $query->get();
        $this->info('Creating placeholder engines for models without engines…');
        $created = 0;
        foreach ($models as $model) {
            $exists = CarEngine::where('car_maker_id', $model->car_maker_id)
                ->where('car_model_id', $model->id)
                ->exists();
            if ($exists) { continue; }
            CarEngine::updateOrCreate([
                'car_maker_id' => $model->car_maker_id,
                'car_model_id' => $model->id,
                'label' => 'Unknown',
            ], [
                'raw' => null,
            ]);
            $created++;
        }
        $this->info("Engines step completed. Created: {$created} placeholder engines.");
        return self::SUCCESS;
    }

    private function http()
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'User-Agent' => 'myGarage/1.0 (+https://example.com)'
        ])->retry(2, 300);
    }
}


