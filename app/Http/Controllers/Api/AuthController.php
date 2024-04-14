<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required",
            "password" => "required"
        ]);

        // validate email is phone or email
        // $isPhone = preg_match("/^[0-9]+$/", $request->email);
        $isEmail = filter_var($request->email, FILTER_VALIDATE_EMAIL);

        $user = User::where("email", $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Email or password not valid"], 401);
        }
        $token = $user->createToken($request->device)->plainTextToken;
        $user->token = $token;

        return response()->json($user);
    }
    public function register(Request $request)
    {
        $request->validate([

        ]);
    }
}
