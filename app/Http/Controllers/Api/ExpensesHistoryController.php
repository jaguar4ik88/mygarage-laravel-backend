<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpensesHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ExpensesHistoryController extends Controller
{
    public function index(Request $request, $userId)
    {
        try {
            Log::info('API Request: GET /history/' . $userId, [
                'user_id' => $userId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $user = User::findOrFail($userId);

            $query = ExpensesHistory::where('user_id', $userId)
                ->with(['vehicle', 'expenseType.translationGroup.translations'])
                ->orderBy('service_date', 'desc');

            // Filter by expense_type_id
            if ($request->filled('expense_type_id')) {
                $query->where('expense_type_id', $request->query('expense_type_id'));
            }

            // Pagination
            $perPage = $request->query('per_page', 20);
            $page = $request->query('page', 1);
            
            $expensesHistory = $query->paginate($perPage, ['*'], 'page', $page);

            // Add legacy type field for backward compatibility
            // Add type field for backward compatibility
            $expensesHistory->each(function ($expense) {
                if ($expense->expenseType) {
                    $expense->type = $expense->expenseType->slug;
                }
            });

            Log::info('API Response: GET /history/' . $userId, [
                'user_id' => $userId,
                'records_count' => $expensesHistory->count(),
                'current_page' => $expensesHistory->currentPage(),
                'last_page' => $expensesHistory->lastPage(),
                'total' => $expensesHistory->total()
            ]);

            return response()->json([
                'success' => true,
                'data' => $expensesHistory->items(),
                'pagination' => [
                    'current_page' => $expensesHistory->currentPage(),
                    'last_page' => $expensesHistory->lastPage(),
                    'per_page' => $expensesHistory->perPage(),
                    'total' => $expensesHistory->total(),
                    'has_more' => $expensesHistory->hasMorePages()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: GET /history/' . $userId, [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expenses history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $userId)
    {
        try {
            Log::info('API Request: POST /history/' . $userId . '/add', [
                'user_id' => $userId,
                'data' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required|exists:vehicles,id',
                'type' => 'sometimes|string|in:maintenance,repair,inspection,fuel',
                'expense_type_id' => 'sometimes|exists:expense_types,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost' => 'required|numeric|min:0',
                'mileage' => 'required|integer|min:0',
                'service_date' => 'required|date',
                'station_name' => 'nullable|string|max:255',
            ]);

            // Ensure expense_type_id is provided
            if (!$request->has('expense_type_id')) {
                $validator->errors()->add('expense_type_id', 'expense_type_id is required');
            }

            if ($validator->fails()) {
                Log::warning('API Validation Error: POST /history/' . $userId . '/add', [
                    'user_id' => $userId,
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify user exists
            $user = User::findOrFail($userId);

            // Check if vehicle belongs to user
            $vehicle = $user->vehicles()->findOrFail($request->vehicle_id);

            $expenseData = $request->all();
            $expenseData['user_id'] = $userId;

            // Set type field based on expense_type_id for backward compatibility
            if ($request->has('expense_type_id')) {
                $expenseType = \App\Models\ExpenseType::find($request->expense_type_id);
                if ($expenseType) {
                    $expenseData['type'] = $expenseType->slug;
                } else {
                    $expenseData['type'] = 'other'; // fallback
                }
            } elseif (!$request->has('type')) {
                $expenseData['type'] = 'other'; // fallback
            }

            $expensesHistory = ExpensesHistory::create($expenseData);

            Log::info('API Response: POST /history/' . $userId . '/add', [
                'user_id' => $userId,
                'expense_id' => $expensesHistory->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense record created successfully',
                'data' => $expensesHistory
            ], 201);
        } catch (\Exception $e) {
            Log::error('API Error: POST /history/' . $userId . '/add', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create expense record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $userId, $id)
    {
        try {
            Log::info('API Request: PUT /history/' . $userId . '/update/' . $id, [
                'user_id' => $userId,
                'expense_id' => $id,
                'data' => $request->all(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $expensesHistory = ExpensesHistory::where('user_id', $userId)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|string|in:maintenance,repair,inspection,fuel',
                'expense_type_id' => 'sometimes|exists:expense_types,id',
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'cost' => 'sometimes|required|numeric|min:0',
                'mileage' => 'sometimes|required|integer|min:0',
                'service_date' => 'sometimes|required|date',
                'station_name' => 'nullable|string|max:255',
            ]);

            // Set type field based on expense_type_id for backward compatibility
            if ($request->has('expense_type_id')) {
                $expenseType = \App\Models\ExpenseType::find($request->expense_type_id);
                if ($expenseType) {
                    $request->merge(['type' => $expenseType->slug]);
                }
            }

            if ($validator->fails()) {
                Log::warning('API Validation Error: PUT /history/' . $userId . '/update/' . $id, [
                    'user_id' => $userId,
                    'expense_id' => $id,
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $expensesHistory->update($request->all());

            Log::info('API Response: PUT /history/' . $userId . '/update/' . $id, [
                'user_id' => $userId,
                'expense_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense record updated successfully',
                'data' => $expensesHistory
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: PUT /history/' . $userId . '/update/' . $id, [
                'user_id' => $userId,
                'expense_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update expense record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $userId, $id)
    {
        try {
            Log::info('API Request: DELETE /history/' . $userId . '/delete/' . $id, [
                'user_id' => $userId,
                'expense_id' => $id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $expensesHistory = ExpensesHistory::where('user_id', $userId)->findOrFail($id);
            $expensesHistory->delete();

            Log::info('API Response: DELETE /history/' . $userId . '/delete/' . $id, [
                'user_id' => $userId,
                'expense_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense record deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: DELETE /history/' . $userId . '/delete/' . $id, [
                'user_id' => $userId,
                'expense_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete expense record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(Request $request, $userId)
    {
        try {
            Log::info('API Request: GET /history/' . $userId . '/static', [
                'user_id' => $userId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $user = User::findOrFail($userId);

            // Get total expenses
            $totalExpenses = ExpensesHistory::where('user_id', $userId)->sum('cost');

            // Get expenses by type
            $expensesByType = ExpensesHistory::where('user_id', $userId)
                ->join('expense_types', 'expenses_history.expense_type_id', '=', 'expense_types.id')
                ->selectRaw('expense_types.slug as type, SUM(expenses_history.cost) as total_cost, COUNT(*) as count')
                ->groupBy('expense_types.slug')
                ->get();

            // Get monthly expenses for the last 12 months (DB-agnostic aggregation)
            $monthlyExpensesRaw = ExpensesHistory::where('user_id', $userId)
                ->where('service_date', '>=', now()->subMonths(12))
                ->orderBy('service_date')
                ->get(['service_date', 'cost']);

            $monthlyMap = [];
            foreach ($monthlyExpensesRaw as $expense) {
                $monthKey = $expense->service_date?->format('Y-m');
                if (!$monthKey) {
                    // If service_date is null or unparsable, skip
                    continue;
                }
                if (!array_key_exists($monthKey, $monthlyMap)) {
                    $monthlyMap[$monthKey] = 0.0;
                }
                $monthlyMap[$monthKey] += (float) $expense->cost;
            }

            ksort($monthlyMap);
            $monthlyExpenses = collect($monthlyMap)->map(function ($total, $month) {
                return ['month' => $month, 'total_cost' => $total];
            })->values();

            // Get average expense per record
            $averageExpense = ExpensesHistory::where('user_id', $userId)->avg('cost');

            // Get most expensive expense
            $mostExpensive = ExpensesHistory::where('user_id', $userId)
                ->orderBy('cost', 'desc')
                ->first();

            $statistics = [
                'total_expenses' => $totalExpenses,
                'average_expense' => round($averageExpense, 2),
                'total_records' => ExpensesHistory::where('user_id', $userId)->count(),
                'expenses_by_type' => $expensesByType,
                'monthly_expenses' => $monthlyExpenses,
                'most_expensive' => $mostExpensive ? [
                    'id' => $mostExpensive->id,
                    'title' => $mostExpensive->title,
                    'cost' => $mostExpensive->cost,
                    'date' => $mostExpensive->service_date->format('Y-m-d')
                ] : null
            ];

            Log::info('API Response: GET /history/' . $userId . '/static', [
                'user_id' => $userId,
                'total_expenses' => $totalExpenses,
                'total_records' => $statistics['total_records']
            ]);

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: GET /history/' . $userId . '/static', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expense statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function byVehicle(Request $request, $vehicleId)
    {
        try {
            Log::info('API Request: GET /vehicles/' . $vehicleId . '/history', [
                'vehicle_id' => $vehicleId,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Get vehicle first to ensure it exists
            $vehicle = \App\Models\Vehicle::findOrFail($vehicleId);
            
            // Get expenses for this vehicle
            $query = ExpensesHistory::where('vehicle_id', $vehicleId)
                ->with(['vehicle', 'expenseType.translationGroup.translations'])
                ->orderBy('service_date', 'desc');

            if ($request->filled('expense_type_id')) {
                $query->where('expense_type_id', $request->query('expense_type_id'));
            }

            $expensesHistory = $query->get();

            // Add legacy type field for backward compatibility
            // Add type field for backward compatibility
            $expensesHistory->each(function ($expense) {
                if ($expense->expenseType) {
                    $expense->type = $expense->expenseType->slug;
                }
            });

            Log::info('API Response: GET /vehicles/' . $vehicleId . '/history', [
                'vehicle_id' => $vehicleId,
                'records_count' => $expensesHistory->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $expensesHistory
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: GET /vehicles/' . $vehicleId . '/history', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vehicle expense history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}