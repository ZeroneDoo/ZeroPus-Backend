<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $data = Transaction::query();

        return response()->json($data->paginate($request->limit ?? 20));
    }

    public function store(Request $request)
    {
        $request->validate([
            "book_id" => "required",
        ]);

        $user = User::find(auth()->user()->id);
        $book = Book::find($request->book_id);
        
        if(!$book) {
            return response()->json([
                "message" => "Book not found",
            ], 404);
        }
        if($user->credit < $book->amount)
        {
            return response()->json([
                "message" => "Credit not enough"
            ], 403);
        }

        $data = Transaction::create([
            "user_id" => $user->id,
            "book_id" => $book->id,
            "freezed_credit" => $book->amount,
            "status" => "PENDING"
        ]);
        $userUpdate = $user->update([
            "credit" => $user->credit - $book->amount
        ]);

        if(!$data || $userUpdate === 0)
        {
            return response()->json([
                "message" => "Failed create transaction"
            ], 500);
        }

        return response()->json($data);
    }
}
