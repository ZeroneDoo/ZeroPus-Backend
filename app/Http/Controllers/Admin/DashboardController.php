<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index() 
    {
        $category = Category::get()->count();
        $book = Book::get()->count();
        $user = User::get()->count();
        $role = Role::get()->count();
        return view("dashboard", compact("category", "book", "user", "role"));
    }
}
