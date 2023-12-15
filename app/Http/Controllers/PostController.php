<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Friend;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PostController extends Controller
{
    public function create_post(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|max:255',
            'visibility' => 'required',
        ]);

        $post = new Post();
        $post->user_id = Auth::id();
        $post->post = $validated["message"];
        $post->visibility = $validated["visibility"];
        $post->save();

        return back()->with("success", "Great! You've created a new post!");
    }

    public function retrieve_user_posts(Request $request)
    {
        return Inertia::render('Home', [
            'paginated' => Post::query()->where('user_id', Auth::id())
                ->with(['user:id,profile_image,name,username'])
                ->orderBy('created_at','desc')
                ->cursorPaginate(10)
                ->through(fn(Post $post) => [
                    'id' => $post->id,
                    'visibility' => $post->visibility,
                    'post' => $post->post,
                    'is_liked' => $post->like()->where('user_id', Auth::id())->exists(),
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'like_count' => $post->like->count(),
                    'comment_count' => $post->comments->count(),
                    'user' => [
                        'id'=> $post->user->id,
                        'profile_image' => $post->user->profile_image,
                        'name' => $post->user->name,
                        'username' => $post->user->username
                    ]
                ])
        ]);
    }

    public function retrieve_friends_posts(Request $request)
    {
        return Inertia::render('Home', [
            'paginated' => Post::query()
                ->where('user_id', Auth::id())
                ->orWhere(function ($query){
                    $query->whereIn('user_id', Friend::select('friend_id')->where('user_id', Auth::id())->where('accepted_request', true))
                        ->whereIn('visibility', ['friends', 'public']);
                })->with(['user:id,profile_image,name,username'])
                ->orderBy('created_at','desc')
                ->cursorPaginate(10)
                ->through(fn(Post $post) => [
                    'id' => $post->id,
                    'visibility' => $post->visibility,
                    'post' => $post->post,
                    'is_liked' => $post->like()->where('user_id', Auth::id())->exists(),
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'like_count' => $post->like->count(),
                    'comment_count' => $post->comments->count(),
                    'user' => [
                        'id'=> $post->user->id,
                        'profile_image' => $post->user->profile_image,
                        'name' => $post->user->name,
                        'username' => $post->user->username
                    ]
                ])
        ]);
    }

    public function retrieve_all_posts(Request $request)
    {
        return Inertia::render('Home', [
            'paginated' => Post::query()
                ->where('user_id', Auth::id())
                ->orWhere('visibility', 'public')
                ->orWhere(function ($query){
                    $query->whereIn('user_id', Friend::select('friend_id')->where('user_id', Auth::id())->where('accepted_request', true))
                        ->where('visibility', 'friends');
                })->with(['user:id,profile_image,name,username'])
                ->orderBy('created_at','desc')
                ->cursorPaginate(10)
                ->through(fn(Post $post) => [
                    'id' => $post->id,
                    'visibility' => $post->visibility,
                    'post' => $post->post,
                    'is_liked' => $post->like()->where('user_id', Auth::id())->exists(),
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'like_count' => $post->like->count(),
                    'comment_count' => $post->comments->count(),
                    'user' => [
                        'id'=> $post->user->id,
                        'profile_image' => $post->user->profile_image,
                        'name' => $post->user->name,
                        'username' => $post->user->username,
                        'is_friend' => function () use ($post){
                            $friend = Friend::query()
                                ->where('user_id', Auth::id())
                                ->where('friend_id', $post->user_id)
                                ->first();

                            $inverse = Friend::query()
                                ->where('user_id', $post->user_id)
                                ->where('friend_id', Auth::id())
                                ->first();

                            if(isset($friend) && $friend->accepted_request == 1){
                                return "friend";
                            }else if(isset($friend) && $friend->accepted_request == 0) {
                                return "pending";
                            }else if(isset($inverse) && $inverse->accepted_request == 0){
                                return "requested";
                            }else if(Auth::id() == $post->user_id){
                                return "self";
                            }
                            
                            return "no";
                        }
                    ]
                ]),
        ]);
    }

    public function edit(Request $request, $id){
        $validated = $request->validate([
            "message" => "required|max:255",
            "visibility" => "required"
        ]);

        $post = Post::find($id);
        $post->post = $validated["message"];
        $post->visibility = $validated["visibility"];
        $post->save();

        return back()->with('success', 'You\'ve successfully edited your post!');
    }

    public function delete(Request $request, $id){
        Post::destroy($id);
        Log::debug(Like::whereHasMorph('liked', [Post::class], function ($query) use ($id){
            $query->where('liked_id', $id);
        })->get());
        Like::where('liked_type', Post::class)
            ->where('liked_id', $id)->delete();
        Comment::where('post_id', $id)->delete();
        return back()->with('success', 'You\'ve deleted your post');
    }

    public function like_post(Request $request, int $id){
        $post = Post::find($id);

        $like = new Like(['user_id' => Auth::id()]);
        $like->liked()->associate($post);
        $like->save();

        //TODO: Add notificaton to the user whose post was liked

        return back()->with('success', 'You\'ve liked a post!');
    }

    public function unlike_post(Request $request, int $id){
        $like = Like::where('user_id', Auth::id())->whereHasMorph('liked', [Post::class], function ($query) use ($id) {
            $query->where('liked_id', $id);
        });
        $like->delete();

        return back()->with('success', 'You\'ve unliked the post.');
    }
}
