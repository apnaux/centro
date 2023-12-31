<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\Post;
use App\Models\User;
use App\Notifications\AcceptedFriendRequestNotification;
use App\Notifications\FriendRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FriendController extends Controller
{
    public function add_friend_request(Request $request, string $username)
    {
        //ID corresponds to the ID of the Friend
        $user_to_add = User::where("username", $username)->first();
        Log::debug($user_to_add->get());

        $check_if_exist = Friend::where('user_id', $user_to_add->id)->where('friend_id', Auth::id())->first();
        if (isset($check_if_exist)) {
            $this->accept_friend_request($request, $check_if_exist->user->username);
            return 0;
        }

        $friend = new Friend();
        $friend->user_id = Auth::id();
        $friend->friend_id = $user_to_add->id;
        $friend->save();

        $user_to_add->notify(new FriendRequestNotification($friend->id, $friend, User::find(Auth::id())));

        return back()->with('success', 'You\'ve sent a request to @' . $username);
    }

    public function accept_friend_request(Request $request, string $username)
    {
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

        $notified_user = $request->user();
        $notified_user->notifications()
            ->where('type', FriendRequestNotification::class)
            ->whereJsonContains('data->friend_request->id', $existing_request->id)
            ->delete();

        $existing_request->user->notify(new AcceptedFriendRequestNotification($existing_request->id, $friend_request, User::find(Auth::id())));

        return back()->with('success', 'You are now friends with @' . $requesting_user->username);
    }

    public function delete_friend_request(Request $request, int $id)
    {
        //ID corresponds to the primary key in the friends table
        Friend::destroy($id);

        return back()->with("success", "The friend request has been deleted.");
    }

    public function index(Request $request)
    {
        return [
            'friendRequests' => Friend::where('friend_id', $request->user()->id)
                ->where('accepted_request', false)
                ->with(['user:id,name,username,profile_image'])->get(),
        ];
    }

    public function check_friend_request(Request $request, $id)
    {
        $post = Post::where('id', $id)->first();
        return [
            'is_friend' => $post->user->friends()->where('friend_id', Auth::id())->where('accepted_request', true)->exists() || $post->user->id == Auth::id(),
            'friend_request' => [
                'pending' => Friend::where('user_id', Auth::id())->where('friend_id', $post->user->id)->where('accepted_request', false)->exists(),
                'confirm' => $post->user->friends()->where('friend_id', Auth::id())->where('accepted_request', false)->exists(),
            ]
        ];
    }

    public function check_count(Request $request)
    {
        return [
            'friendRequestCount' => Friend::where('friend_id', Auth::id())->where('accepted_request', 0)->count()
        ];
    }

    public function search_page()
    {
        return Inertia::render('Search');
    }

    public function search_user(Request $request, $query)
    {
        return [
            'search' => User::query()
                ->where('username', 'like', '%' . $query . '%')
                ->orWhere('name', 'like', '%' . $query . '%')
                ->where('username', '!=', $request->user()->username)
                ->with('friends')
                ->cursorPaginate(15)
                ->through(fn(User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'profile_image' => $user->profile_image,
                    'friend' => [
                        'is_friend' => $user->friends()->where('friend_id', Auth::id())->where('accepted_request', true)->exists() || $user->id == Auth::id(),
                        'friend_request' => [
                            'pending' => Friend::where('user_id', Auth::id())->where('friend_id', $user->id)->where('accepted_request', false)->exists(),
                            'confirm' => $user->friends()->where('friend_id', Auth::id())->where('accepted_request', false)->exists(),
                        ]
                    ]
                ]),
        ];
    }
}
