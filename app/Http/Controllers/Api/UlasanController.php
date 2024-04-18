<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function store(Request $request) 
    {
        $user = auth()->user();
        $request->validate([
            "book_id" => "required",
            "rate" => "required",
            "description" => "required"
        ]);

        $book = Book::find($request->book_id);
        if(!$book){
            return response()->json([
                "message"=>"Book not found"
            ], 404);
        }

        $data = Ulasan::create([
            "user_id" => $user->id,
            "book_id" => $request->book_id,
            "rate" => $request->rate,
            "description" => $request->description
        ]);
        $data->user = $user;

        if(!$data){
            return response()->json(["message" => "Failed to create a review"], 500);
        }

        return response()->json($data);
    }

    public function show($id) 
    {
        $data = Ulasan::with("user")->find($id);

        if(!$data)
        {
            return response()->json([
                "message"=> "Review not found"
            ], 404);
        }

        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $ulasan = Ulasan::with("user")->find($id);
        if(!$ulasan){
            return response()->json([
                "message"=>"Review not found"
            ], 404);
        }
        $book = Book::find($request->book_id);
        if(!$book){
            return response()->json([
                "message"=>"Book not found"
            ], 404);
        }

        $data = $ulasan->update([
            "rate" => $request->rate ?? $ulasan->rate,
            "description" => $request->description ?? $ulasan->description
        ]);

        if($data === 0){
            return response()->json(["message" => "Failed to update a review"], 500);
        }

        return response()->json($data);
    }

    public function delete($id)
    {
        $data = Ulasan::find($id);

        if($data)
        {
            return response()->json(["message"=>"Review not found"], 404);
        }
        $data->delete();

        return response()->json(["message"=>"succes deleted review"]);
    }
}
