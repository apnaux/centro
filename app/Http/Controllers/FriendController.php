<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use App\Notifications\AcceptedFriendRequestNotification;
use App\Notifications\FriendRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FriendController extends Controller
{
    public function add_friend_request(Request $request, string $username){
        //ID corresponds to the ID of the Friend
        $user_to_add = User::where("username", $username)->first();
        Log::debug($user_to_add->get());

        $check_if_exist = Friend::where('user_id', $user_to_add->id)->where('friend_id', Auth::id())->first();
        if(isset($check_if_exist)){
            $this->accept_friend_request($request, $check_if_exist->user->username);
            return 0;
        }

        $friend = new Friend();
        $friend->user_id = Auth::id();
        $friend->friend_id = $user_to_add->id;
        $friend->save();

        $user_to_add->notify(new FriendRequestNotification($friend, User::find(Auth::id())));
        
        return back()->with('success', 'You\'ve sent a request to @' . $username);
    }

    public function accept_friend_request(Request $request, string $username){
        $requesting_user = User::where('username', $username)->first();

        Log::debug($requesting_user);

        $existing_request = Friend::where('user_id', $requesting_user->id)->where('friend_id', Auth::id())->first();
        $existing_request->accepted_request = true;

        $friend_request = new Friend();
        $friend_request->user_id = Auth::id();
        $friend_request->friend_id = $existing_request->user_id;
        $friend_request->accepted_request = true;

        $existing_request->save();
        $friend_request->save();

        $existing_request->user->notify(new AcceptedFriendRequestNotification($friend_request, User::find(Auth::id())));

        return back()->with('success', 'You are now friends with @' . $requesting_user->username);
    }

    public function delete_friend_request(Request $request, int $id){
        //ID corresponds to the primary key in the friends table
        Friend::destroy($id);

        return back()->with("success", "The friend request has been deleted.");
    }
}
