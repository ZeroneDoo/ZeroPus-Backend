<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $data = Category::query();
        if($request->has("search"))  {
            $data->where("name" , $request->search);
        }
        
        return response()->json($data->get());
    }
}
