<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarRecommendation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CarRecommendationController extends Controller
{
    /**
     * Получить список рекомендаций
     */
    public function index(Request $request): JsonResponse
    {
        $query = CarRecommendation::query();

        // Фильтрация по марке и модели
        if ($request->has('maker')) {
            $query->where('maker', $request->maker);
        }
        
        if ($request->has('model')) {
            $query->where('model', $request->model);
        }
        
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Фильтрация по пробегу
        // Игнорируем пробег в выборке рекомендаций по запросу

        // Фильтрация по типу обслуживания
        if ($request->has('item')) {
            $query->forItem($request->item);
        }

        // Включение связанных данных
        $query->with(['manualSection', 'translations']);

        // Сортировка по пробегу
        $query->orderBy('mileage_interval', 'asc');

        $recommendations = $query->get();

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Получить рекомендации для конкретного автомобиля
     */
    public function forCar(Request $request): JsonResponse
    {
        $request->validate([
            'maker' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
            'mileage' => 'nullable|integer',
        ]);

        // Базовая выборка по марке и модели (год фильтруем вручную из-за периодов типа "2015-2018")
        $query = CarRecommendation::forCar(
            $request->maker,
            $request->model,
            null
        );

        if ($request->has('mileage')) {
            $query->forMileage($request->mileage);
        }

        $recommendations = $query->with(['manualSection', 'translations'])
            ->orderBy('mileage_interval', 'asc')
            ->get();

        // Если передан год, фильтруем по периодам в поле year (например: "2015-2018", "до 2015", "2019-2023")
        if ($request->filled('year')) {
            $targetYear = (int) $request->year;
            $recommendations = $recommendations->filter(function ($rec) use ($targetYear) {
                $raw = (string) $rec->year;
                $period = trim(mb_strtolower($raw));
                if ($period === '') return true;

                // Нормализуем разные тире к '-'
                $period = str_replace(["–", "—", "−"], "-", $period);
                // Убираем служебные слова
                $period = preg_replace('/\bгод(а|ов)?\b/iu', '', $period);
                $period = preg_replace('/\s+/u', ' ', trim($period));

                // Если встречаются годы, используем их
                if (preg_match_all('/\d{4}/', $period, $all)) {
                    $years = array_map('intval', $all[0]);
                    if (count($years) === 1) {
                        return $years[0] === $targetYear;
                    }
                    if (count($years) >= 2) {
                        $start = min($years[0], $years[1]);
                        $end = max($years[0], $years[1]);
                        return $targetYear >= $start && $targetYear <= $end;
                    }
                }

                // Текстовые формы
                if (preg_match('/^до\s*(\d{4})/iu', $period, $m)) {
                    return $targetYear <= (int) $m[1];
                }
                if (preg_match('/^(после|с)\s*(\d{4})/iu', $period, $m)) {
                    return $targetYear >= (int) $m[2];
                }

                // Неизвестный формат — оставим (лучше показать, чем скрыть)
                return true;
            })->values();

            // Если после фильтрации ничего нет, показываем все периоды для марки+модели (пусто быть не должно)
            if ($recommendations->isEmpty()) {
                $recommendations = $query->with(['manualSection', 'translations'])
                    ->orderBy('mileage_interval', 'asc')
                    ->get();
            }
        }

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Получить конкретную рекомендацию
     */
    public function show(CarRecommendation $carRecommendation): JsonResponse
    {
        $carRecommendation->load('manualSection');

        return response()->json([
            'success' => true,
            'data' => $carRecommendation,
        ]);
    }

    /**
     * Создать новую рекомендацию
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'maker' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer',
            'mileage_interval' => 'required|integer',
            'item' => 'required|string|max:255',
            'recommendation' => 'required|string',
            'manual_section_id' => 'nullable|exists:manual_sections,id',
        ]);

        $recommendation = CarRecommendation::create($request->all());
        $recommendation->load('manualSection');

        return response()->json([
            'success' => true,
            'data' => $recommendation,
            'message' => 'Рекомендация успешно создана',
        ], 201);
    }

    /**
     * Обновить рекомендацию
     */
    public function update(Request $request, CarRecommendation $carRecommendation): JsonResponse
    {
        $request->validate([
            'maker' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer',
            'mileage_interval' => 'sometimes|required|integer',
            'item' => 'sometimes|required|string|max:255',
            'recommendation' => 'sometimes|required|string',
            'manual_section_id' => 'nullable|exists:manual_sections,id',
        ]);

        $carRecommendation->update($request->all());
        $carRecommendation->load('manualSection');

        return response()->json([
            'success' => true,
            'data' => $carRecommendation,
            'message' => 'Рекомендация успешно обновлена',
        ]);
    }

    /**
     * Удалить рекомендацию
     */
    public function destroy(CarRecommendation $carRecommendation): JsonResponse
    {
        $carRecommendation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Рекомендация успешно удалена',
        ]);
    }

    /**
     * Получить список уникальных марок
     */
    public function makers(): JsonResponse
    {
        $makers = CarRecommendation::distinct()
            ->pluck('maker')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $makers,
        ]);
    }

    /**
     * Получить список моделей для марки
     */
    public function models(Request $request): JsonResponse
    {
        $request->validate([
            'maker' => 'required|string',
        ]);

        $models = CarRecommendation::where('maker', $request->maker)
            ->distinct()
            ->pluck('model')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $models,
        ]);
    }

    /**
     * Получить список типов обслуживания
     */
    public function items(): JsonResponse
    {
        $items = CarRecommendation::distinct()
            ->pluck('item')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}