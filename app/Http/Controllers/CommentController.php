<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(Request $request, $id){
        return [
            'comments' => Comment::with('user:id,name,username,profile_image')->where('post_id', $id)->cursorPaginate(10),
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
}
