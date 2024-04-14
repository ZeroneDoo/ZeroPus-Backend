<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix("admin")->group(function () {
    Route::get("/login", [AuthController::class, "index"])->middleware("guest")->name("login");
    Route::post("/login", [AuthController::class, "login"])->middleware("guest")->name("login.store");

    Route::post("/logout", [AuthController::class, "logout"])->name("logout");
    
    Route::get("/", [DashboardController::class, "index"])->name("dashboard")->middleware("auth");

    // book
    Route::middleware(["auth"])->prefix("book")->name("book.")->group(function () {
        Route::get("/", [BookController::class, "index"])->name("index")->middleware("can:view book");
        Route::post("/store", [BookController::class, "store"])->name("store")->middleware("can:create book");
        Route::post("/update/{id}", [BookController::class, "update"])->name("update")->middleware("can:update book");
        Route::delete("/delete/{id}", [BookController::class, "delete"])->name("delete")->middleware("can:delete book");
        Route::get("/get", [BookController::class, "getData"])->name("getData");
    });
    // category
    Route::middleware(["auth"])->prefix("category")->name("category.")->group(function () {
        Route::get("/", [CategoryController::class, "index"])->name("index")->middleware("can:view category");
        Route::post("/store", [CategoryController::class, "store"])->name("store")->middleware("can:create category");
        Route::post("/update/{id}", [CategoryController::class, "update"])->name("update")->middleware("can:update category");
        Route::delete("/delete/{id}", [CategoryController::class, "delete"])->name("delete")->middleware("can:delete category");
        Route::get("/get", [CategoryController::class, "getData"])->name("getData");
    });
    // credit
    Route::middleware(["auth"])->prefix("credit")->name("credit.")->group(function () {
        Route::get("/", [CreditController::class, "index"])->name("index")->middleware("can:view credit");
        Route::post("/store", [CreditController::class, "store"])->name("store")->middleware("can:create credit");
        Route::post("/update/{id}", [CreditController::class, "update"])->name("update")->middleware("can:update credit");
        Route::delete("/delete/{id}", [CreditController::class, "delete"])->name("delete")->middleware("can:delete credit");
        Route::get("/get", [CreditController::class, "getData"])->name("getData");
    });
    // user
    Route::middleware(["auth"])->prefix("user")->name("user.")->group(function () {
        Route::get("/", [UserController::class, "index"])->name("index")->middleware("can:view user");
        Route::post("/store", [UserController::class, "store"])->name("store")->middleware("can:create user");
        Route::post("/update/{id}", [UserController::class, "update"])->name("update")->middleware("can:update user");
        Route::delete("/delete/{id}", [UserController::class, "delete"])->name("delete")->middleware("can:delete user");
        Route::get("/get", [UserController::class, "getData"])->name("getData");
    });
    // role
    Route::middleware(["auth"])->prefix("role")->name("role.")->group(function () {
        Route::get("/", [RoleController::class, "index"])->name("index")->middleware("can:view role");
        Route::post("/store", [RoleController::class, "store"])->name("store")->middleware("can:create role");
        Route::post("/update/{id}", [RoleController::class, "update"])->name("update")->middleware("can:update role");
        Route::delete("/delete/{id}", [RoleController::class, "delete"])->name("delete")->middleware("can:delete role");
        Route::get("/get", [RoleController::class, "getData"])->name("getData");
    });
});