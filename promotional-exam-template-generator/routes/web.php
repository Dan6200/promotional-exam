<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'TemplateController@index');
Route::post('/upload', 'TemplateController@upload');
Route::get('/generate', 'TemplateController@generate');
