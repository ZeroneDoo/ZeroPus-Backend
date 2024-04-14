<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view("category.index");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
        ]);

        $data = Category::create([
            "name" => $request->name,
        ]); 

        if(!$data) {
            return response()->json([
                "message" => "Failed to create category"
            ], 500);
        }

        return response()->json($data);
    }

    public function update(Request $request, $id) 
    {
        $category = Category::find($id);
        if(!$category) { 
            return response()->json([
                "message" => "Category not found"
            ], 404);
        }
        $data = $category->update([
            "name" => $request->name ?? $category->name,
        ]); 

        if($data === 0) {
            return response()->json([
                "message" => "Failed to update"
            ], 500);
        }

        return response()->json($category);
    }

    public function delete($id)
    {
        $data = Category::find($id);

        if(!$data) {
            return response()->json(['message' => "Category not found"], 404);
        }

        $data->delete();

        return response()->json(['message' => "Category deleted"]);
    }

    public function getData()
    {
        try {
            $draw = request()->input('draw');
            $start = request()->input('start');
            $length = request()->input('length');
            $searchValue = request()->input('search.value');

            $format = [];
            $datas = Category::query();

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
                    // "created_at" => Carbon::parse($data->created_at, "Asia/Jakarta")->toDateTimeString(),
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
