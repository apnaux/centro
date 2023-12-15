<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\NotificationController;
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

    Route::get('/home/public', [PostController::class, 'retrieve_all_posts']);

    Route::get('/home/notifications', [NotificationController::class, 'index']);

    Route::get('/home/notifications/count', [NotificationController::class, 'count']);

    Route::get('/home/notifications/delete/{id}', [NotificationController::class, 'delete']);

    Route::delete('/post/{id}/delete', [PostController::class, 'delete']);

    Route::get('/post/{id}/comments', [CommentController::class, 'index']);

    Route::patch('/post/{id}/edit', [PostController::class, 'edit']);

    Route::post('/post/{id}/like', [PostController::class, 'like_post']);

    Route::post('/post/{id}/unlike', [PostController::class, 'unlike_post']);

    Route::post('/post/create', [PostController::class, 'create_post']);

    Route::post('/post/{id}/comments/create', [CommentController::class, 'create']);

    Route::delete('/post/{pid}/comments/{cid}/delete', [CommentController::class, 'delete']);

    Route::post('/post/{pid}/comments/{cid}/like', [CommentController::class, 'like']);

    Route::delete('/post/{pid}/comments/{cid}/unlike', [CommentController::class, 'unlike']);

    Route::post('/friend/request/add/{username}', [FriendController::class, 'add_friend_request']);

    Route::post('/friend/request/accept/{username}', [FriendController::class, 'accept_friend_request']);

    Route::delete('/friend/request/delete/{id}', [FriendController::class, 'delete_friend_request']);

    Route::get('/friend/request', [FriendController::class, 'index']);

    Route::get('/friend/request/count', [FriendController::class, 'check_count']);

    Route::post('/logout', [AccountController::class, 'logout']);
});
