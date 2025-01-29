<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\TeacherLoginController;
use App\Http\Controllers\Auth\TeacherRegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('/users', UserController::class)->except(['index', 'store', 'create', 'show']);
Route::resource('/timetables', TimetableController::class)->except(['edit', 'update', 'destroy']);;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::group([ // 認証されていないとアクセスできない
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'role:admin'] // ここでまとめて指定
], function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
});

// ログイン等は認証前にあくせすするため、middlewareを指定しない
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('login', [AdminLoginController::class, 'store'])->name('login.store');
    Route::post('logout', [AdminLoginController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('login', [TeacherLoginController::class, 'create'])->name('login');
    Route::post('login', [TeacherLoginController::class, 'store'])->name('login.store');
    Route::get('register', [TeacherRegisterController::class, 'create'])->name('register');
    Route::post('register', [TeacherRegisterController::class, 'store'])->name('register.store');
    Route::post('logout', [TeacherLoginController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin and Teacher Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'staff',
    'as' => 'staff.',
    'middleware' => ['auth', 'role:admin,teacher'] // ここでまとめて指定
], function () {
    Route::get('timetable', [TimetableController::class, 'create'])->name('timetable.create');
    Route::post('timetable', [TimetableController::class, 'store'])->name('timetable.store');
    Route::get('timetable/edit', [TimetableController::class, 'edit'])->name('timetable.edit');
    Route::put('timetable', [TimetableController::class, 'update'])->name('timetable.update');
    Route::delete('timetable', [TimetableController::class, 'delete'])->name('timetable.delete');
});

// 認証が必要なプロファイル関連のルート
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 認証関連のルートを読み込み
require __DIR__.'/auth.php';
