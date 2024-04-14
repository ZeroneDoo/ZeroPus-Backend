<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $permissions = Permission::get();
        return view("role.index", compact("permissions"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required"
        ]);
        // dd($request->all());

        $role = Role::create([
            "name" => $request->name,
            "guard_name" => $request->guard_name
        ]);
        if(!$role){
            return response()->json([
                "message" => "Failed to create role"
            ], 500);
        }
        if($request->permissions) 
        {
            $permissions = Permission::whereIn("id", $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }
        return response()->json($role);

    }

    public function update(Request $request, $id) 
    {
        $role = Role::find($id);

        if(!$role) {
            return response()->json(["message" => "Role not found"], 404);
        }
        $permissions = $this->insertOrDelete($request->permissions, $role);
        if(!$permissions) {
            return response()->json(["message" => "Failed update book"], 500);
        }

        $data = $role->update([
            "name" => $request->name ?? $role->name
        ]);

        if($data === 0) {
            return response()->json([
                "messaage" => "Failed to update role"
            ], 500);
        }

        return response()->json($role);
    }

    public function delete($id)
    {
        $data = Role::find($id)->delete();
        
        if(!$data) {
            return response()->json([
                "message" => "Role not found",
            ], 404);
        }
        
    }

    public function insertOrDelete($array, Role $role)
    {
        try {
            DB::beginTransaction();
            foreach($array as $permission){
                DB::table("role_has_permissions")->updateOrInsert([
                    "permission_id" => $permission,
                    "role_id" => $role->id,
                ], [
                    "permission_id" => $permission,
                    "role_id" => $role->id,
                ]);
            }

            $getPermissions = $role->permissions()->get()->pluck("id")->toArray();
            $deletedId = array_diff($getPermissions, $array);
            DB::table("role_has_permissions")->whereIn("permission_id", $deletedId)->where("role_id", $role->id)->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    public function getData()
    {
        try {
            $draw = request()->input('draw');
            $start = request()->input('start');
            $length = request()->input('length');
            $searchValue = request()->input('search.value');

            $format = [];
            $datas = Role::query();

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
                    "permissions" => $data->permissions()->get()->pluck("id"),
                    "created_at" => $data->created_at->diffForHumans(),
                    "updated_at" => $data->updated_at->diffForHumans(),
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
