<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function login(Request $request){
        $validated = $request->validate([
            'username' => 'required',
            'password'=> 'required',
        ]);
    
        if(Auth::attempt($validated)) {
            $request->session()->regenerate();
            return redirect('/home');
        }
        return redirect('/');
    }

    public function register(Request $request){
        $validated = $request->validate([
            'username' => 'unique:users|required|min:8|max:18',
            'password'=> 'required|min:8',
        ]);
    
        $user = new User();
        $user->name = 'Test';
        $user->username = $validated['username'];
        $user->password = $validated['password'];
        $user->save();
    
        if(Auth::attempt($validated)){
            $request->session()->regenerate();
            return redirect('/home');
        }
    
        return redirect('/');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');  
    }
}
