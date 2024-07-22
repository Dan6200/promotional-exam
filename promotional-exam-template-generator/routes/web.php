<?php

use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TemplateController::class, 'index']);
Route::post('/upload', [TemplateController::class, 'upload']);
Route::get('/generate', [TemplateController::class, 'generate'])->name('generate');
Route::get('/download/{fileName}', [TemplateController::class, 'download'])->name('download');
