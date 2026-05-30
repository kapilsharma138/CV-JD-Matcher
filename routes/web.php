<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScoreController;

Route::get('/', [ScoreController::class, 'index']);
Route::post('/score', [ScoreController::class, 'score']);