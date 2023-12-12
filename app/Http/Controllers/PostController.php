<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PostController extends Controller
{
    public function create_post(Request $request){
        $validated = $request->validate([
            'message' => 'required|max:255',
            'visibility' => 'required',
        ]);

        $post = new Post();
        $post->user_id = Auth::id();
        $post->post = $validated["message"];
        $post->visibility = $validated["visibility"];
        $post->save();

        return redirect('/home');
    }

    public function retrieve_user_posts(Request $request){
        return Inertia::render('Home', [
            'posts' => Post::query()->where('user_id', Auth::id())->get(),
        ]);
    }

    public function retrieve_all_posts(Request $request){
        $user = User::find(Auth::id());
        $friends = array();

        if(isset($user->friends)){
            foreach($user->friends as $friend){
                array_push($friends, $friend->friend_id);
            }

            return Inertia::render('Home', [
                'posts' => Post::query()
                    ->where(function ($query) use ($friends){
                        $query->where('user_id', Auth::id())
                            ->orWhere('visibility', 'public')
                            ->orWhereIn('user_id', $friends);
                    })
                    ->get(),
            ]);
        }

        return Inertia::render('Home', [
            'posts' => 'You have no friends :<'
        ]);
    }
}
