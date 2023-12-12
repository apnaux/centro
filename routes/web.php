<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return Inertia::render('Login');
})->name('login');

Route::post('/login', [AccountController::class, 'login']);

Route::post('/register', [AccountController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return redirect('/home/me');
    })->name('home');

    Route::get('/home/me', [PostController::class, 'retrieve_user_post']);
    Route::get('/home/all', [PostController::class, 'retrieve_all_post']);

    Route::post('/create/post', [PostController::class, 'create_post']);

    Route::post('/logout', [AccountController::class, 'logout']);
});
