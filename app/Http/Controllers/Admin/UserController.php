<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::get();
        return view("user.index", compact("roles"));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "username" => "required",
            "email" => "required",
            "no_telp" => "required",
            "credit" => "required",
            "role" => "required",
        ]);

        $file = [];
        if($request->hasFile("profile")) {
            $file['profile'] = Storage::disk("public")->put("user/profile", $request->profile);
        }else{
            $file['profile'] = null;
        }

        $data = User::create([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
            "no_telp" => $request->no_telp,
            "credit" => $request->credit,
            "alamat" => $request->alamat,
            "password" => Hash::make($request->password),
            "profile" => $file['profile'],
        ]);

        if(!$data) {
            return response()->json(["message" => "Failed to create user"], 500);
        }
        if($request->role) {
            $role = Role::find($request->role);
            $data->assignRole($role);  
        }

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }
        
        $profile = substr($user->profile, strlen(url("/")) + 9); // remove baseurl
        $data = [
            "name" => $request->name ?? $user->name,
            "username" => $request->username ?? $user->username,
            "email" => $request->email ?? $user->email,
            "no_telp" => $request->no_telp ?? $user->no_telp,
            "credit" => $request->credit ?? $user->credit,
            "alamat" => $request->alamat ?? $user->alamat,
        ];
        if($request->hasFile("profile")) {
            // delete file if exists
            if(Storage::disk("public")->exists("$profile")) {
                Storage::disk("public")->delete("$profile");
            }
            $data['profile'] = Storage::disk("public")->put("user/profile", $request->profile);
        }else{
            if(stripos($user->profile, url("/")) !== false) {
                $data['profile'] = $profile;
            }else{
                $data['profile'] = null;
            }
        }
        // role
        if($request->role) {
            $isExists = DB::table("model_has_roles")->where("role_id", $user?->roles?->first()?->id)->where("model_id", $user->id);
            if($isExists->first()) {
                $isExists->update([
                    "role_id" => $request->role
                ]);
            }else{
                $role = Role::find($request->role);
                $user->assignRole($role);  
            }
        }
        if($request->password) $data['password'] = Hash::make($request->password);
        
        $data = $user->update($data);
        
        if($data === 0) {
            return response()->json([
                "message" => "Failed update user"
            ], 500);
        }

        return response()->json($user);
    }

    public function delete($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json([
                "message" => "User not found"
            ], 404);
        }

        $user->delete();

        return response()->json([
            "message" => "User deleted"
        ]);
    }

    public function getData()
    {
        try {
            $draw = request()->input('draw');
            $start = request()->input('start');
            $length = request()->input('length');
            $searchValue = request()->input('search.value');

            $format = [];
            $datas = User::query();

            // search 
            if($searchValue) {
                $datas->where("name", "like", "%$searchValue%");
            }

            $totalRecords = $datas->count();

            $datas->skip($start)->take($length);

            foreach( $datas->get() as $data)
            {
                $format[] = [
                    "id" => $data->id,
                    "name" => $data->name,
                    "username" => $data->username,
                    "email" => $data->email,
                    "no_telp" => $data->no_telp,
                    "alamat" => $data->alamat,
                    "credit" => $data->credit,
                    "profile" => $data->profile,
                    "role" => $data->roles->first(),
                    "action" => "
                        <div class=\"btn-group\" role=\"group\">
                            <button data-id=\"$data->id\" onclick=\"getData(this)\" class=\"btn btn-warning text-white\" data-toggle=\"modal\" data-target=\"#modal\"><i class=\"fas fa-edit\"></i></button>
                            <button data-id=\"$data->id\" onclick=\"deleteData(this)\" class=\"btn btn-danger text-white\"><i class=\"far fa-trash-alt\"></i></button>
                        </div>
                    "
                ];
            }

            $response = [
                "draw" => $draw,
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "data" => $format,
            ];
        
            return response()->json($response);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ]);
        }
    }
}

