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
    Route::get('/home', [PostController::class, 'index'])->name('home');

    Route::get('/home/retrieve', [PostController::class, 'retrieve_friends_posts']);

    Route::get('/home/public', [PostController::class, 'index_public']);

    Route::get('/home/public/retrieve', [PostController::class, 'retrieve_all_posts']);

    Route::get('/home/notifications', [NotificationController::class, 'index']);

    Route::get('/home/notifications/count', [NotificationController::class, 'count']);

    Route::get('/home/notifications/delete/{id}', [NotificationController::class, 'delete']);

    Route::get('/profile/me', [AccountController::class, 'show_profile']);

    Route::get('/profile/{username}', [AccountController::class, 'show_profile']);

    Route::get('/posts/user/{username}', [PostController::class, 'retrieve_user_posts']);

    Route::get('/post/{id}', [PostController::class, 'retrieve_post']);
    
    Route::delete('/post/{id}/delete', [PostController::class, 'delete']);

    Route::get('/post/{id}/comments/count', [CommentController::class, 'comment_count']);

    Route::get('/post/{id}/comments', [CommentController::class, 'index']);

    Route::patch('/post/{id}/edit', [PostController::class, 'edit']);

    Route::post('/post/{id}/like', [PostController::class, 'like_post']);

    Route::get('/post/{id}/like/count', [PostController::class, 'like_count']);

    Route::get('/post/{id}/like/check', [PostController::class, 'get_if_liked']);

    Route::get('/post/{id}/friend/check', [FriendController::class, 'check_friend_request']);

    Route::delete('/post/{id}/unlike', [PostController::class, 'unlike_post']);

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

    Route::get('/search', [FriendController::class, 'search_page']);

    Route::get('/search/{query}', [FriendController::class, 'search_user']);

    Route::post('/logout', [AccountController::class, 'logout']);
});
