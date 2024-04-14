<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);

        $user = User::where("email", $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Email or password not valid"], 401);
        }

        if(isset($request->remember)) {
            setcookie("remember", $request->email, 0);
        }else{
            unset($_COOKIE['remember']);
        }


        Auth::login($user);

        return response()->json(["message" => "Login successful"], 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect()->route("login");
    }
}
