<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Ulasan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $user = request()->user("sanctum");
        $data = Book::with("ulasan", "ulasan.user")->inRandomOrder();
        $q = $request->search;
        if($request->has("search"))
        {
            $data->where(function (Builder $subQuery) use ($q) {
                $subQuery->where("title", "like", "%$q%");
            });
        }       
        
        $paginateData = $data->paginate($request->limit ?? 20);

        $datas = $paginateData->getCollection()->map(function($book) use($user) {
            $book->rate = $book->ulasan()->avg("rate") ?? 0.0;
            $book->is_save = in_array($user?->id, $book->bookmark()->pluck("user_id")->toArray());
            $book->bookmark_count = $book->bookmark()->count();
            $book->ulasan_count = $book->ulasan()->count();
            $book->hasUlasan = $book->ulasan()->where("user_id", $user?->id)->first();
            $book->ulasan = Ulasan::with("user")->where("book_id", $book->id)->orderBy("updated_at", "DESC")->get();
            return $book;
        });

        $paginateData->setCollection($datas);  

        return response()->json($paginateData);
    }
    public function popular(Request $request) 
    {
        $user = request()->user("sanctum");
        $paginateData = Ulasan::with("book")->select('book_id', DB::raw('AVG(rate) AS rate'))
        ->groupBy('book_id')
        ->orderByDesc('rate')
        ->limit(12)
        ->paginate();

        $datas = $paginateData->getCollection()->map(function ($book) use($user) {
            $book = $book->book;
            $book->rate = $book->ulasan()->avg("rate") ?? 0.0;
            $book->is_save = in_array($user?->id, $book->bookmark()->pluck("user_id")->toArray());
            $book->bookmark_count = $book->bookmark()->count();
            $book->ulasan_count = $book->ulasan()->count();
            $book->hasUlasan = $book->ulasan()->where("user_id", $user?->id)->first();
            $book->ulasan = Ulasan::with("user")->where("book_id", $book->id)->orderBy("updated_at", "DESC")->get();
            return $book;
        });

        $paginateData->setCollection($datas);  

        return response()->json($paginateData);
    }
    public function show($id) 
    {
        $user = request()->user("sanctum");
        $data = Book::find($id);
        
        if(!$data) return response()->json(["message" => "Book not found"], 404);

        $data->rate = $data->ulasan()->avg("rate") ?? 0.0;
        $data->is_save = in_array($user?->id, $data->bookmark()->pluck("user_id")->toArray());
        $data->bookmark_count = $data->bookmark()->count();
        $data->ulasan_count = $data->ulasan()->count();
        $data->hasUlasan = $data->ulasan()->where("user_id", $user?->id)->first();
        $data->ulasan = Ulasan::with("user")->where("book_id", $id)->orderBy("updated_at", "DESC")->get();

        return response()->json($data);
    }
}
