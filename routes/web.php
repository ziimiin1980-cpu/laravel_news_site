<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalistTextController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category', [HomeController::class, 'categories'])->name('categories.index');
Route::get('/category/{category}', [HomeController::class, 'showCategory'])->name('categories.show');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'not-blocked', 'role:journalist,admin'])->group(function () {
    Route::get('/journalist', [JournalistTextController::class, 'index'])->name('journalist');
    Route::post('/journalist/texts', [JournalistTextController::class, 'store'])->name('journalist.texts.store');
    Route::get('/journalist/texts/{text}/edit', [JournalistTextController::class, 'edit'])->name('journalist.texts.edit');
    Route::put('/journalist/texts/{text}', [JournalistTextController::class, 'update'])->name('journalist.texts.update');
});

Route::middleware(['auth', 'not-blocked', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::put('/admin/texts/{text}', [AdminController::class, 'updateText'])->name('admin.texts.update');
    Route::delete('/admin/texts/{text}', [AdminController::class, 'deleteText'])->name('admin.texts.delete');
    Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.role');
    Route::patch('/admin/users/{user}/block', [AdminController::class, 'updateBlock'])->name('admin.users.block');
});
