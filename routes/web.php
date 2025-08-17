<?php

use Illuminate\Support\Facades\Route;
use App\Events\OrderCreated;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/fire-event', function () {
    event(new OrderCreated(['id' => 1, 'status' => 'created']));
    return "Event Fired!";
});
