<?php

namespace App\Http\Controllers;

use App\Notifications\FriendRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return [
            'notifications' => $request->user()->notifications()->cursorPaginate(15),
        ];
    }

    public function count(Request $request){
        return $request->user()->unreadNotifications()->count();
    }

    public function delete(Request $request, $id) {
        $notification = $request->user->notifications()->where('id', $id)->first();
        $notification->delete();

        return back()->with('success', 'The notification has been deleted');
    }
}
