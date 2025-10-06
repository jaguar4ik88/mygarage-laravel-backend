<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\ServiceHistoryController;
use App\Http\Controllers\Api\ExpensesHistoryController;
use App\Http\Controllers\Api\ServiceStationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ManualController;
use App\Http\Controllers\Api\AdviceController;
use App\Http\Controllers\Api\CarDataController;
use App\Http\Controllers\Api\ExpenseTypeController;
use App\Http\Controllers\Api\PrivacyPolicyController;
use App\Http\Controllers\Api\CarRecommendationController;
use App\Http\Controllers\Api\CarTyreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/google', [AuthController::class, 'googleAuth']);

// Public API routes (only for testing/development)
// NOTE: Remove public access to user/profile before release
Route::get('/stations/nearby', [ServiceStationController::class, 'nearby']); // Legacy alias

// Public GET routes protected by API key (if configured)
Route::middleware('api.key')->group(function () {
    Route::apiResource('manuals', ManualController::class)->only(['index', 'show']);
    Route::get('/advice', [AdviceController::class, 'index']);

    // Car data cached proxy routes
    Route::get('/car-data/makers', [CarDataController::class, 'makers']);
    Route::get('/car-data/models', [CarDataController::class, 'models']);
    Route::get('/car-data/trims', [CarDataController::class, 'trims']);

    // Public data routes
    Route::get('/reminder-types', [App\Http\Controllers\Api\ReminderTypeController::class, 'index']);
    Route::get('/manual-sections', [App\Http\Controllers\Api\ManualSectionController::class, 'index']);
    Route::get('/advice-sections', [App\Http\Controllers\Api\AdviceSectionController::class, 'index']);
    Route::get('/expense-types', [ExpenseTypeController::class, 'index']);
    Route::get('/faq', [App\Http\Controllers\Api\FaqController::class, 'index']);
    Route::get('/faq/categories', [App\Http\Controllers\Api\FaqController::class, 'categories']);
    Route::get('/faq/questions', [App\Http\Controllers\Api\FaqController::class, 'questions']);
    
    // Car recommendations and tyres routes
    Route::get('/car-recommendations', [CarRecommendationController::class, 'index']);
    Route::get('/car-recommendations/for-car', [CarRecommendationController::class, 'forCar']);
    Route::get('/car-recommendations/makers', [CarRecommendationController::class, 'makers']);
    Route::get('/car-recommendations/models', [CarRecommendationController::class, 'models']);
    Route::get('/car-recommendations/items', [CarRecommendationController::class, 'items']);
    Route::get('/car-recommendations/{carRecommendation}', [CarRecommendationController::class, 'show']);
    
    Route::get('/car-tyres', [CarTyreController::class, 'index']);
    Route::get('/car-tyres/for-car', [CarTyreController::class, 'forCar']);
    Route::get('/car-tyres/brands', [CarTyreController::class, 'brands']);
    Route::get('/car-tyres/models', [CarTyreController::class, 'models']);
    Route::get('/car-tyres/dimensions', [CarTyreController::class, 'dimensions']);
    Route::get('/car-tyres/dimensions-for-car', [CarTyreController::class, 'dimensionsForCar']);
    Route::get('/car-tyres/{carTyre}', [CarTyreController::class, 'show']);
});

// Google Places API routes
Route::get('/google-places/nearby-search', [App\Http\Controllers\Api\GooglePlacesController::class, 'nearbySearch']);
Route::get('/google-places/place-details', [App\Http\Controllers\Api\GooglePlacesController::class, 'placeDetails']);
Route::get('/google-places/text-search', [App\Http\Controllers\Api\GooglePlacesController::class, 'textSearch']);
Route::get('/google-places/photo', [App\Http\Controllers\Api\GooglePlacesController::class, 'photo']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    
    // Reminder routes (protected)
    Route::apiResource('reminders', ReminderController::class);
    Route::get('/users/{userId}/reminders', [ReminderController::class, 'byUser']);
    
    // Vehicle routes (protected)
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('/vehicles/{id}/manual', [VehicleController::class, 'manual']);
    Route::post('/vehicles/{id}/manual/pdf', [VehicleController::class, 'uploadManualPdf']);
    
    // Service Station routes (protected)
    Route::get('/service-stations', [ServiceStationController::class, 'index']);
    Route::get('/service-stations/{userId}', [ServiceStationController::class, 'byUser']);
    Route::post('/service-stations/add', [ServiceStationController::class, 'store']);
    Route::delete('/service-stations/delete/{id}', [ServiceStationController::class, 'destroy']);
    Route::put('/service-stations/update/{id}', [ServiceStationController::class, 'update']);
    Route::get('/service-stations/nearby', [ServiceStationController::class, 'nearby']);
    
    // History routes (protected)
    Route::get('/history/{userId}', [ExpensesHistoryController::class, 'index']);
    Route::post('/history/{userId}/add', [ExpensesHistoryController::class, 'store']);
    Route::put('/history/{userId}/update/{id}', [ExpensesHistoryController::class, 'update']);
    Route::delete('/history/{userId}/delete/{id}', [ExpensesHistoryController::class, 'destroy']);
    Route::get('/history/{userId}/static', [ExpensesHistoryController::class, 'statistics']);
    Route::get('/vehicles/{vehicleId}/history', [ExpensesHistoryController::class, 'byVehicle']);
    
    // Admin routes for car recommendations and tyres
    Route::apiResource('car-recommendations', CarRecommendationController::class)->except(['index', 'show']);
    Route::apiResource('car-tyres', CarTyreController::class)->except(['index', 'show']);
    
});

// Public privacy policy routes
Route::get('/privacy-policy/{language}', [PrivacyPolicyController::class, 'show']);

// Admin privacy policy routes (protected)
Route::middleware('api.key')->group(function () {
    Route::get('/admin/privacy-policy', [PrivacyPolicyController::class, 'index']);
    Route::post('/admin/privacy-policy', [PrivacyPolicyController::class, 'store']);
    Route::put('/admin/privacy-policy/{privacyPolicy}', [PrivacyPolicyController::class, 'update']);
    Route::delete('/admin/privacy-policy/{privacyPolicy}', [PrivacyPolicyController::class, 'destroy']);
});
