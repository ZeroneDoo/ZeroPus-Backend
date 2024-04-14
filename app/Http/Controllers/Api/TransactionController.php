<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {

    }

    public function store(Request $request)
    {
        $request->validate([
            "book_id" => "required",
            "freezed_credit" => "required"
        ]);
    }
}
