<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/send-email', function () {
    return view('resources/views/emails/default.blade.php');
});

