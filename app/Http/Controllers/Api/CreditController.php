<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $data = Credit::query();
        if($request->has("search"))  {
            $data->where("name" , $request->search);
        }
        
        return response()->json($data->get());
    }
}
