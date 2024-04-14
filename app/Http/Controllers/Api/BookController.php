<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $data = Book::inRandomOrder();
        $q = $request->search;
        if($request->has("search"))
        {
            $data->where(function (Builder $subQuery) use ($q) {
                $subQuery->where("title", "like", "%$q%");
            });
        }
        return response()->json($data->paginate($request->limit ?? 20));
    }
}
