<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarTyre;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CarTyreController extends Controller
{
    /**
     * Получить список рекомендаций по шинам
     */
    public function index(Request $request): JsonResponse
    {
        $query = CarTyre::query();

        // Фильтрация по марке и модели автомобиля
        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }
        
        if ($request->has('model')) {
            $query->where('model', $request->model);
        }
        
        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        // Фильтрация по размеру шин
        if ($request->has('dimension')) {
            $query->forDimension($request->dimension);
        }

        // Сортировка по году (новые сначала)
        $query->orderBy('year', 'desc');

        $tyres = $query->get();

        return response()->json([
            'success' => true,
            'data' => $tyres,
        ]);
    }

    /**
     * Получить рекомендации по шинам для конкретного автомобиля
     */
    public function forCar(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
        ]);

        $tyres = CarTyre::forCar(
            $request->brand,
            $request->model,
            $request->year
        )->orderBy('year', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tyres,
        ]);
    }

    /**
     * Получить конкретную рекомендацию по шинам
     */
    public function show(CarTyre $carTyre): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $carTyre,
        ]);
    }

    /**
     * Создать новую рекомендацию по шинам
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer',
            'dimension' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $tyre = CarTyre::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $tyre,
            'message' => 'Рекомендация по шинам успешно создана',
        ], 201);
    }

    /**
     * Обновить рекомендацию по шинам
     */
    public function update(Request $request, CarTyre $carTyre): JsonResponse
    {
        $request->validate([
            'brand' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'year' => 'sometimes|required|integer',
            'dimension' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $carTyre->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $carTyre,
            'message' => 'Рекомендация по шинам успешно обновлена',
        ]);
    }

    /**
     * Удалить рекомендацию по шинам
     */
    public function destroy(CarTyre $carTyre): JsonResponse
    {
        $carTyre->delete();

        return response()->json([
            'success' => true,
            'message' => 'Рекомендация по шинам успешно удалена',
        ]);
    }

    /**
     * Получить список уникальных марок автомобилей
     */
    public function brands(): JsonResponse
    {
        $brands = CarTyre::distinct()
            ->pluck('brand')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $brands,
        ]);
    }

    /**
     * Получить список моделей для марки
     */
    public function models(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string',
        ]);

        $models = CarTyre::where('brand', $request->brand)
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
     * Получить список размеров шин
     */
    public function dimensions(): JsonResponse
    {
        $dimensions = CarTyre::distinct()
            ->pluck('dimension')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $dimensions,
        ]);
    }

    /**
     * Получить список размеров шин для конкретного автомобиля
     */
    public function dimensionsForCar(Request $request): JsonResponse
    {
        $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'nullable|integer',
        ]);

        $query = CarTyre::where('brand', $request->brand)
            ->where('model', $request->model);

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        $dimensions = $query->distinct()
            ->pluck('dimension')
            ->sort()
            ->values();

        return response()->json([
            'success' => true,
            'data' => $dimensions,
        ]);
    }
}