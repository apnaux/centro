<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return Inertia::render("Notifications", [
            'notifications' => $request->user()->notifications()->cursorPaginate(15),
        ]);
    }
}
