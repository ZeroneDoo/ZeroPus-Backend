<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if(!$transaction) {
            return response()->json(["message" => "Transaction not found"], 404);
        }

        $file = [];
        if($request->hasFile("photo")) {
            $file['photo'] = Storage::disk("public")->put("transaction/photo", $request->photo);
        }else{
            $file['photo'] = null;
        }

        $data = $transaction->update([
            "status" => $request->status ?? $transaction->status,
            "photo" => $file['photo'] ?? $transaction->photo,
            "description" => $request->description ?? $transaction->description,
            "tanggal_pengembalian" => $request->tanggal_pengembalian ?? $transaction->tanggal_pengembalian,
        ]);
        if($data === 0) 
        {
            return response()->json([
                "message" => "Failed to update transaction"
            ], 500);
        }

        return response()->json($transaction);
    }
    public function getData()
    {
        try {
            $draw = request()->input('draw');
            $start = request()->input('start');
            $length = request()->input('length');
            $searchValue = request()->input('search.value');

            $format = [];
            $datas = Transaction::with("user", "book")->query();

            // search 
            if($searchValue) {
                $datas->where("status", "like", "%$searchValue%");
            }

            $totalRecords = $datas->count();

            $datas->skip($start)->take($length);

            foreach( $datas->get() as $data)
            {
                $format[] = [
                    "id" => $data->id,
                    "user" => $data->title,
                    "description" => $data->description,
                    "penulis" => $data->penulis,
                    "penerbit" => $data->penerbit,
                    "amount" => $data->amount,
                    "source" => $data->source,
                    "photo" => $data->photo,
                    "category" => $data->category()->get()->pluck("id"),
                    "is_rent" => $data->is_rent,
                    "stock" => $data->stock,
                    "tahun_terbit" => $data->tahun_terbit,
                    "action" => "
                        <div class=\"btn-group\" role=\"group\">
                            <button data-id=\"$data->id\" onclick=\"getData(this)\" class=\"btn btn-warning text-white\" data-toggle=\"modal\" data-target=\"#modal\"><i class=\"fas fa-edit\"></i></button>
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
