<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TeacherRegisterController extends Controller
{
    /**
     * 教員登録画面を表示
     */
    public function create()
    {
        return view('auth.TeacherRegister');
    }

    /**
     * 教員登録処理
     */
    public function store(Request $request)
    {
        // 追加: バリデーションを直接コントローラー内で実施
        $request->validate([
            'school_id' => 'required|string|max:255|unique:users,school_id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|string|in:teacher', // 修正: role は必須で 'teacher' のみ
        ]);

        // 新規ユーザーの作成
        $user = User::create([
            'school_id' => $request->input('school_id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
        ]);

        // ユーザーをログインさせる
        Auth::login($user);

        return redirect()->route('timetables.index')->with('success', '登録が完了しました。');
    }
}
