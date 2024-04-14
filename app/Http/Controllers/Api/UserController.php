<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show()
    {
        $data = auth()->user();

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $data = [
            "name" => $request->name ?? $user->name,
            "username" => $request->username ?? $user->username,
            "email" => $request->email ?? $user->email,
            "alamat" => $request->alamat ?? $user->alamat,
            "password" => $request->password ?? $user->password,
            "credit" => $request->credit ?? $user->credit,
        ];
        
        if($request->has("profile"))
        {
            $dirPath = "user/profile";
            $extension = explode("/",$request->profile['type'])[1];
            $fileName = base64_encode($request->profile['name']).".".$extension;
            
            if(Storage::disk("public")->exists("$user->profile")) {
                Storage::disk("public")->delete("$user->profile");
            }
            $storage = Storage::disk("public")->put($dirPath . '/' . $fileName, base64_decode($request->profile['uri']));

            if($storage){
                $data['profile'] = $dirPath . '/' . $fileName;
            }else{
                DB::rollback();
                return response()->json(["message" => "Failed to update profile picture"], 401);
            }
        }


        $user->update($data);
        
    }
}
