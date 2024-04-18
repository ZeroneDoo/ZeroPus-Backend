<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request) 
    {
        $user = User::find(auth()->user()->id);
        $data = $user->bookmark()->with("book", "user")->paginate($request->limit ?? 20);
        // $bookId = $user->bookmark()->get()->pluck("book_id")->toArray();

        // $data = Book::whereIn("id",$bookId)->paginate($request->limit ?? 20);

        return response()->json($data);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            "book_id" => "required"            
        ]);

        $user = auth()->user();
        $book = Book::find($request->book_id);
        if(!$book) {
            return response()->json([
                "message" => "Book not found"
            ], 404);
        }
        $isExists = Bookmark::where("user_id", $user->id)->where("book_id", $book->id)->first();
        if($isExists) {
            $isExists->delete();
            return response()->json(["message" => "Success remove book from bookmark"]);
        }else{
            $data = Bookmark::create([
                "user_id" => $user->id,
                "book_id" => $book->id
            ]);
            if(!$data)
            {
                return response()->json([
                    "message" => "Failed store book to bookmark"
                ], 500);
            }
            return response()->json(["message" => "Success insert book into bookmark"]);
        }
    }
}
