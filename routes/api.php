<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UlasanController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 

Route::middleware("auth:sanctum")->prefix("profile")->group(function () {
    Route::get("/", [UserController::class, "show"]);
    Route::post("/update", [UserController::class, "update"]);
});
Route::prefix("auth")->group(function () {
    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/register", [AuthController::class, "register"]);
});
Route::prefix("book")->group(function () {
    Route::get("/", [BookController::class, "index"]);
    Route::get("/popular", [BookController::class, "popular"]);
    Route::get("/{id}", [BookController::class, "show"]);
});
Route::prefix("credit")->group(function () {
    Route::get("/", [CreditController::class, "index"]);
});
Route::prefix("category")->group(function () {
    Route::get("/", [CategoryController::class, "index"]);
});
Route::prefix("payment")->group(function () {
    Route::post("/create", [PaymentController::class, "createInvoice"])->middleware("auth:sanctum");
    Route::post("/callback", [PaymentController::class, "callback"]);
});
Route::middleware("auth:sanctum")->prefix("bookmark")->group(function () {
    Route::get("/", [BookmarkController::class, "index"]);
    Route::post("/create", [BookmarkController::class, "store"]);
});
Route::middleware("auth:sanctum")->prefix("ulasan")->group(function () {
    Route::post("/create", [UlasanController::class, "store"]);
    Route::post("/update/{id}", [UlasanController::class, "update"]);
    Route::get("/{id}", [UlasanController::class, "show"]);
    Route::delete("/delete/{id}", [UlasanController::class, "delete"]);
});