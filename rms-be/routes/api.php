<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/menu', [MenuController::class, 'currentMenu']);
Route::get('/venues/{venue}/menu', [MenuController::class, 'venueMenu']);
Route::get('/tables/{table}/menu', [MenuController::class, 'tableMenu']);

Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
Route::post('/payments/{payment}/receipt', [OrderController::class, 'receipt']);

Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::post('/admin/menu-items', [MenuController::class, 'store']);
Route::patch('/admin/menu-items/{menuItem}', [MenuController::class, 'update']);
Route::patch('/admin/menu-items/{menuItem}/availability', [MenuController::class, 'toggle']);
Route::delete('/admin/menu-items/{menuItem}', [MenuController::class, 'destroy']);
Route::post('/admin/uploads/menu-image', [UploadController::class, 'storeMenuImage']);
