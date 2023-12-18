<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function login(Request $request){
        $validated = $request->validate([
            'username' => 'required|exists:users,username',
            'password'=> 'required',
        ]);
    
        if(Auth::attempt($validated)) {
            $request->session()->regenerate();
            return redirect('/hello');
        }
        return redirect('/');
    }

    public function register(Request $request){
        $validated = $request->validate([
            'username' => 'unique:users|required|min:8|max:18',
            'password'=> 'required|min:8',
        ]);
    
        $user = new User();
        $user->username = $validated['username'];
        $user->password = $validated['password'];
        $user->save();
    
        if(Auth::attempt($validated)){
            $request->session()->regenerate();
            return redirect('/');
        }
    
        return redirect('/');
    }

    public function set_credentials(Request $request){
        $validated = $request->validate([
            'name' => 'required|max:32',
            'bio' => 'max:64'
        ]);

        $user = User::find(Auth::id());
        $user->name = $validated['name'];
        $user->bio = $validated['bio'];
        $user->save();

        return redirect('/home');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');  
    }

    public function show_profile(Request $request, $username = null){
        if(isset($username)){
            $user = User::where('username', $username)->first();
            return Inertia::render('Profile', [
                'profile' => [
                    'user' => $user,
                    'friendsCount' => $user->friends()->where('accepted_request', true)->count(),
                    'postsCount' => $user->posts()->count(),
                    'likesCount' => $user->likes()->where('liked_type', Post::class)->count(),
                ]
            ]);
        }

        return Inertia::render('Profile', [
            'profile' => [
                'user' => Auth::user(),
                'friendsCount' => $request->user()->friends()->where('accepted_request', true)->count(),
                'postsCount' => $request->user()->posts()->count(),
                'likesCount' => $request->user()->likes()->where('liked_type', Post::class)->count(),
            ]
        ]);
    }
}
