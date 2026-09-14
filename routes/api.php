<?php

use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\FeedLogController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\WeighInController;
use Illuminate\Support\Facades\Route;

// RBAC is enforced inside controllers via $user->hasPermission('code'), or add
// middleware per-route/group as the permission model matures.

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // --- Batches ---
    Route::get('/batches', [BatchController::class, 'index']);
    Route::post('/batches', [BatchController::class, 'store']);
    Route::get('/batches/{batch}', [BatchController::class, 'show']);
    Route::patch('/batches/{batch}', [BatchController::class, 'update']);
    Route::get('/batches/{batch}/profitability', [BatchController::class, 'profitability']);

    // --- Animals (nested under batches for intake/listing, flat for detail) ---
    Route::get('/batches/{batch}/animals', [AnimalController::class, 'index']);
    Route::post('/animals', [AnimalController::class, 'store']);
    Route::get('/animals/{animal}', [AnimalController::class, 'show']);
    Route::get('/animals/ready-to-sell', [AnimalController::class, 'readyToSell']);

    // --- Weigh-ins (growth tracking) ---
    Route::get('/animals/{animal}/weigh-ins', [WeighInController::class, 'index']);
    Route::post('/animals/{animal}/weigh-ins', [WeighInController::class, 'store']);

    // --- Feed logs (per batch/pen) ---
    Route::get('/batches/{batch}/feed-logs', [FeedLogController::class, 'index']);
    Route::post('/batches/{batch}/feed-logs', [FeedLogController::class, 'store']);

    // --- Health records ---
    Route::get('/animals/{animal}/health-records', [HealthRecordController::class, 'index']);
    Route::post('/animals/{animal}/health-records', [HealthRecordController::class, 'store']);

    // --- Expenses (batch overhead) ---
    Route::get('/batches/{batch}/expenses', [ExpenseController::class, 'index']);
    Route::post('/batches/{batch}/expenses', [ExpenseController::class, 'store']);

    // --- Sales & settlement ---
    Route::post('/sales-orders', [SalesOrderController::class, 'store']);
    Route::get('/sales-orders/{salesOrder}', [SalesOrderController::class, 'show']);

    // --- Payments (against a sales order) ---
    Route::get('/sales-orders/{salesOrder}/payments', [PaymentController::class, 'index']);
    Route::post('/sales-orders/{salesOrder}/payments', [PaymentController::class, 'store']);
});
