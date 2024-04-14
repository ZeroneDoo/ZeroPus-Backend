<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index()
    {
        return view("credit.index");
    }

    public function store(Request $request)
    {
        $request->validate([
            "price" => "required",
            "amount" => "required"
        ]);

        $data = Credit::create([
            "name" => $request->name,
            "price" => $request->price,
            "amount" => $request->amount,
            "is_active" => boolval($request->is_active),
        ]); 

        if(!$data) {
            return response()->json([
                "message" => "Failed to create "
            ], 500);
        }

        return response()->json($data);
    }

    public function update(Request $request, $id) 
    {
        $credit = Credit::find($id);
        if(!$credit) { 
            return response()->json([
                "message" => "Credit not found"
            ], 404);
        }
        $data = $credit->update([
            "name" => $request->name ?? $credit->name,
            "price" => $request->price ?? $credit->amount,
            "amount" => $request->amount ?? $credit->amount,
            "is_active" => boolval($request->is_active),
        ]); 

        if($data === 0) {
            return response()->json([
                "message" => "Failed to update"
            ], 500);
        }

        return response()->json($credit);
    }

    public function delete($id)
    {
        $data = Credit::find($id);

        if(!$data) {
            return response()->json(['message' => "Credit not found"], 404);
        }

        $data->delete();

        return response()->json(['message' => "Credit deleted"]);
    }

    public function getData()
    {
        try {
            $draw = request()->input('draw');
            $start = request()->input('start');
            $length = request()->input('length');
            $searchValue = request()->input('search.value');

            $format = [];
            $datas = Credit::query();

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
                    "price" => $data->price,
                    "amount" => $data->amount,
                    "is_active" => $data->is_active === 1 ? true : false,
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
