<?php

use App\Http\Controllers\ObraController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

//Route::view('/', 'dashboard');
Route::redirect('/', '/jfpanel');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/test-image', function () {
    $path = storage_path('app/public/2025/01/31/4531905d9dc644231bb7fa8b272362f86c89f6dc.png');

    if (!file_exists($path)) {
        return response()->json(['error' => 'Archivo no encontrado'], 404);
    }

    return Response::file($path);
});

require __DIR__ . '/auth.php';
