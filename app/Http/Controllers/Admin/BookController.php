<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryBook;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index()
    {
        $categories = Category::get();
        return view("book.index", compact("categories"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required",
            "description" => "required",
            "penulis" => "required",
            "penerbit" => "required",
        ]);

        dd($request->all());

        $file = [];
        if($request->hasFile("source")) {
            $file['source'] = Storage::disk("public")->put("book/source", $request->source);
        }else{
            $file['source'] = null;
        }
        if($request->hasFile("photo")) {
            $file['photo'] = Storage::disk("public")->put("book/photo", $request->photo);
        }else{
            $file['photo'] = null;
        }

        $data = Book::create([
            "title" => $request->title,
            "description" => $request->description,
            "penulis" => $request->penulis,
            "penerbit" => $request->penerbit,
            "amount" => $request->amount,
            "source" => $file['source'],
            "photo" => $file['photo'],
            "is_rent" => boolval($request->is_rent),
            "stock" => $request->stock,
            "tahun_terbit" => $request->tahun_terbit,
        ]);

        if(!$data) {
            return response()->json(["message" => "Failed to create book"], 500);
        }

        return response()->json($data);
    }

    public function update(Request $request, $id) 
    {
        $book = Book::find($id);
        
        // dd($request->all());
        if(!$book) {
            return response()->json(['message' => "Book not found"], 404);
        }
        $source = substr($book->source, strlen(url("/")) + 9); // remove baseurl
        $photo = substr($book->photo, strlen(url("/")) + 9); // remove baseurl
        $file = [];
        if($request->hasFile("source")) {
            // delete file if exists
            if(Storage::disk("public")->exists("$source")) {
                Storage::disk("public")->delete("$source");
            }
            $file['source'] = Storage::disk("public")->put("book/source", $request->source);
        }else{
            $file['source'] = null;
        }
        if($request->hasFile("photo")) {
            // delete file if exists
            if(Storage::disk("public")->exists("$photo")) {
                Storage::disk("public")->delete("$photo");
            }
            
            $file['photo'] = Storage::disk("public")->put("book/photo", $request->photo);
        }else{
            $file['photo'] = null;
        }
        $category = $this->insertOrDelete($request->category, $book);
        if(!$category) {
            return response()->json(["message" => "Failed update book"], 500);
        }

        $data = $book->update([
            "title" => $request->title ?? $book->title,
            "description" => $request->description ?? $book->description,
            "penulis" => $request->penulis ?? $book->penulis,
            "penerbit" => $request->penerbit ?? $book->penerbit,
            "amount" => $request->amount ?? $book->amount,
            "is_rent" => boolval($request->is_rent),
            "stock" => $request->stock ?? $book->stock,
            "tahun_terbit" => $request->tahun_terbit ?? $book->tahun_terbit,
            "source" => $file['source'] ?? $source,
            "photo" => $file['photo'] ?? $photo,
        ]);

        if($data === 0) {
            return response()->json([
                "message" => "Failed update book"
            ], 500);
        }

        return response()->json($book);
    }

    public function delete($id)
    {
        $book = Book::find($id);

        if(!$book) {
            return response()->json(['message' => "Book not found"],404);
        }

        $book->delete();

        return response()->json(['message' => "Book deleted"]);
    } 

    public function insertOrDelete($array, Book $book)
    {
        try {
            DB::beginTransaction();
            foreach($array as $category){
                CategoryBook::updateOrCreate([
                    "category_id" => $category,
                    "book_id" => $book->id
                ], [
                    "category_id" => $category,
                    "book_id" => $book->id
                ]);
            }

            $getCategories = $book->category()->get()->pluck("id")->toArray();
            $deletedId = array_diff($getCategories, $array);
            CategoryBook::whereIn("category_id", $deletedId)->where("book_id", $book->id)->delete();
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
            $datas = Book::query();

            // search 
            if($searchValue) {
                $datas->where("title", "like", "%$searchValue%");
            }

            $totalRecords = $datas->count();

            $datas->skip($start)->take($length);

            foreach( $datas->get() as $data)
            {
                $format[] = [
                    "id" => $data->id,
                    "title" => $data->title,
                    "description" => $data->description,
                    "penulis" => $data->penulis,
                    "penerbit" => $data->penerbit,
                    "amount" => $data->amount,
                    "source" => $data->source,
                    "photo" => $data->photo,
                    "category" => $data->category()->get()->pluck("id"),
                    "is_rent" => $data->is_rent === 1 ? true : false,
                    "stock" => $data->stock,
                    "tahun_terbit" => $data->tahun_terbit,
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
