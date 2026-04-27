<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\S3Controller;

Route::get('/', [S3Controller::class, 'index']);
Route::post('/upload', [S3Controller::class, 'upload']);
Route::delete('/file/{key}/delete', [S3Controller::class, 'delete'])->where('key', '.*');
Route::post('/file/{key}/change-class', [S3Controller::class, 'changeClass'])->where('key', '.*');
Route::get('/file/{key}/url', [S3Controller::class, 'presignedUrl'])->where('key', '.*');
