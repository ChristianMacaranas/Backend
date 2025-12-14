<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function() {
    return response()->json(['message' => 'Backend connected!']);
});

Route::get('/test', function() {
    return response()->json(['message' => 'Backend connected!']);
});
