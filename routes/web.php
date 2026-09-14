<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContributeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\FacebookController;
use Illuminate\Support\Facades\Route;
Route::get("/", [HomeController::class,"index"])->name("home");
Route::get("/section/{slug}", [HomeController::class,"section"])->name("section");
Route::post("/subscribe", [ContributeController::class,"subscribe"])->name("subscribe");
Route::get("/login", [AuthController::class,"showLogin"])->name("login");
Route::post("/login", [AuthController::class,"login"]);
Route::get("/register", [AuthController::class,"showRegister"])->name("register");
Route::post("/register", [AuthController::class,"register"]);
Route::post("/logout", [AuthController::class,"logout"])->name("logout");
Route::middleware("auth")->post("/contribute", [ContributeController::class,"submit"])->name("contribute");
Route::get('/test-claude', [HomeController::class, 'testClaude']);
Route::middleware("auth")->prefix("moderator")->group(function () {
    Route::get("/", [ModeratorController::class,"dashboard"])->name("moderator.dashboard");
    Route::post("/moderate/{contribution}", [ModeratorController::class,"moderate"])->name("moderator.moderate");
});
Route::middleware("auth")->prefix("admin")->group(function () {
    Route::get("/", [AdminController::class,"dashboard"])->name("admin.dashboard");
    Route::get("/users", [AdminController::class,"users"])->name("admin.users");
    Route::post("/users", [AdminController::class,"createUser"])->name("admin.users.create");
    Route::post("/users/{user}/ban", [AdminController::class,"toggleBan"])->name("admin.users.ban");
    Route::delete("/users/{user}", [AdminController::class,"deleteUser"])->name("admin.users.delete");
    Route::get("/content", [AdminController::class,"content"])->name("admin.content");
    Route::post("/content", [AdminController::class,"saveContent"])->name("admin.content.create");
    Route::delete("/content/{content}", [AdminController::class,"deleteContent"])->name("admin.content.delete");
    Route::get("/sections", [AdminController::class,"sections"])->name("admin.sections");
    Route::get("/subscribers", [AdminController::class,"subscribers"])->name("admin.subscribers");
});


Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/facebook', [FacebookController::class, 'index'])->name('admin.facebook');
    Route::post('/facebook', [FacebookController::class, 'store'])->name('admin.facebook.store');
    Route::post('/facebook/summarize', [FacebookController::class, 'summarize'])->name('admin.facebook.summarize');
    Route::delete('/facebook/{post}', [FacebookController::class, 'destroy'])->name('admin.facebook.destroy');
});