<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request, $id){
        return [
            'comments' => Comment::query()->with('user:id,name,username,profile_image')
                ->where('post_id', $id)->orderBy('created_at','desc')
                ->cursorPaginate(10)
                ->through(fn (Comment $comment) => [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'post_id' => $comment->post_id,
                    'comment' => $comment->comment,
                    'is_liked' => $comment->like()->where('user_id', Auth::id())->exists(),
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'username' => $comment->user->username,
                        'profile_image' => $comment->user->profile_image,
                    ]
                ]),
        ];
    }

    public function create(Request $request, int $id){
        $validated = $request->validate([
            'comment' => 'required|max:127',
        ]);

        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->post_id = $id;
        $comment->comment = $validated['comment'];
        $comment->save();

        return back()->with('success', 'You\'ve posted a comment!');
    }

    public function delete(Request $request, $pid, $cid){
        $comment = Comment::find($cid)->where('post_id', $pid)->first();
        $likes = Like::where('liked_type', Comment::class)->where('liked_id', $cid);

        $comment->delete();
        $likes->delete();

        return back()->with('success', 'You\'ve deleted your comment');
    }

    public function like(Request $request, $pid, $cid){
        $comment = Comment::find($cid)->where('post_id', $pid)->first();
        $like = new Like(['user_id' => Auth::id()]);
        $like->liked()->associate($comment);
        $like->save();

        return back()->with('success', 'You\'ve liked a comment!');
    }

    public function unlike(Request $request, $pid, $cid){
        $like = Like::where('user_id', Auth::id())->whereHasMorph('liked', [Comment::class], function ($query) use ($cid) {
            $query->where('liked_id', $cid);
        });
        $like->delete();

        return back()->with('success', 'You\'ve unliked the comment.');
    }
}
