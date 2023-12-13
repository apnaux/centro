<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FriendController;
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
    Route::get('/home', [PostController::class, 'retrieve_friends_posts'])->name('home');

    Route::get('/home/me', [PostController::class, 'retrieve_user_posts']);

    Route::get('/home/public', [PostController::class, 'retrieve_all_posts']);

    // Route::get('/notifications', [NotificationsController::class, 'index']);

    // Route::post('/post/{id}/like', [PostController::class, 'like_post']);

    Route::post('/post/create', [PostController::class, 'create_post']);

    // Route::post('/post/{id}/create/comment', [CommentController::class, 'comment_to_post']);

    Route::post('/friend/request/add/{username}', [FriendController::class, 'add_friend_request']);

    Route::post('/friend/request/accept/{id}', [FriendController::class, 'accept_friend_request']);

    Route::delete('/friend/request/delete/{id}', [FriendController::class, 'delete_friend_request']);

    Route::post('/logout', [AccountController::class, 'logout']);
});
