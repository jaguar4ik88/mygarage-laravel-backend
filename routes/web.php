<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReminderTypeController;
use App\Http\Controllers\Admin\AdminManualSectionController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminFaqQuestionController;
use App\Http\Controllers\Admin\AdminDataViewController;
use App\Http\Controllers\Admin\AdminDefaultManualController;
use App\Http\Controllers\Admin\AdminAdviceSectionController;
use App\Http\Controllers\Admin\AdminAdviceItemController;
use App\Http\Controllers\Admin\ExpenseTypeController;
use App\Http\Controllers\Admin\PrivacyPolicyController;

// Главная страница
Route::get('/', function () {
    return view('welcome');
});

// Админка - аутентификация
Route::prefix('admin')->name('admin.')->group(function () {
    // Аутентификация
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Защищенные маршруты
    Route::middleware('admin.auth')->group(function () {
        // Главная страница админки
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Управление пользователями
        Route::resource('users', AdminUserController::class);
        
        // Управление системными данными
        Route::resource('reminder-types', AdminReminderTypeController::class);
        Route::resource('manual-sections', AdminManualSectionController::class);
        Route::resource('default-manuals', AdminDefaultManualController::class);
        Route::resource('expense-types', ExpenseTypeController::class);
        Route::resource('privacy-policy', PrivacyPolicyController::class);
        
        // Управление советами
        Route::resource('advice-sections', AdminAdviceSectionController::class);
        Route::resource('advice-items', AdminAdviceItemController::class);
        
        // Управление FAQ
        Route::prefix('faq')->name('faq.')->group(function () {
            Route::resource('categories', AdminFaqController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
            Route::resource('questions', AdminFaqQuestionController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        });
        
        // Просмотр пользовательских данных
        Route::prefix('data')->name('data.')->group(function () {
            Route::get('vehicles', [AdminDataViewController::class, 'vehicles'])->name('vehicles');
            Route::get('vehicles/{vehicle}', [AdminDataViewController::class, 'vehicleShow'])->name('vehicles.show');
            Route::get('statistics', [AdminDataViewController::class, 'statistics'])->name('statistics');
        });
    });
});
