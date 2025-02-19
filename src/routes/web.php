<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\Auth\TeacherLoginController;
use App\Http\Controllers\Auth\TeacherRegisterController;
use App\Http\Controllers\Auth\AdminLoginController;

/*
|--------------------------------------------------------------------------
| 一般user用のルート
|--------------------------------------------------------------------------
*/
Route::get('timetables', [TimetableController::class, 'index'])->name('timetables.index');
Route::resource('/users', UserController::class)->except(['store', 'create', 'show']);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['auth', 'role:admin'] // ここでまとめて指定
], function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
});

// Adminログイン関連
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
| Admin and Teacher (Staff) Routes
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'staff',
    'as' => 'staff.',
    'middleware' => ['auth', 'role:admin,teacher']
], function () {
    Route::resource('timetables', TimetableController::class)->except(['index', 'show', 'edit', 'update', 'destroy']);
    Route::resource('subjects', SubjectController::class)->except(['show']);

    // 担当科目のみを表示する
    Route::get('show/{id}', [TimetableController::class, 'show'])->name('show');

    // 月単位の編集/更新 など カスタムルートを定義
    // （リソースルートの edit($id) ではなく、月単位の一括更新を想定）
    Route::get('timetables/edit',   [TimetableController::class, 'edit'])->name('timetables.edit');
    Route::post('timetables/update',[TimetableController::class, 'update'])->name('timetables.update');
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
|  - 認証ユーザのプロファイル編集。Breeze等が生成。
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Laravel Breeze等の認証関連
require __DIR__.'/auth.php';
