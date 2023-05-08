<?php

use App\Http\Controllers\StatsController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post("/register",        UserController::class . "@registration");
Route::post("/createEventType", StatsController::class . "@createEventType");
Route::post("/submit",          StatsController::class . "@submit");
Route::get("/stats",            StatsController::class . "@stats");


