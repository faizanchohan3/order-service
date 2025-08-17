<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController; // <-- Import karein
use App\Http\Controllers\Api\OwnerDashboardController;
use App\Events\TestBroadcastEvent;
Route::get('/my-orders', [OrderController::class, 'myOrders']);
Route::apiResource('orders', OrderController::class);
Route::get('/restaurants/{restaurant}/orders', [OrderController::class, 'getOrdersForRestaurant']);
//Route::get('/my-restaurant/orders', [OwnerDashboardController::class, 'getOrders'])
Route::patch('/orders/{order}/status',[OrderController::class,'updateStatus']);
Route::get('/test-broadcast', function () {
    broadcast(new TestBroadcastEvent());
    return 'Test event has been broadcast!';
});
