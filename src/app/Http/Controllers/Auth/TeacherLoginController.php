<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherLoginController extends Controller
{
    /**
     * 教員ログイン画面を表示
     */
    public function create()
    {
        return view('auth.TeacherLogin');
    }

    /**
     * 教員ログイン処理
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // ログインしたユーザーが teacher かどうかチェック
        if (Auth::user()->role !== 'teacher') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('teacher.login')->withErrors([
                'email' => '教員としてログインできません。',
            ]);
        }

        return redirect()->route('timetables.index');
    }

    /**
     * 教員ログアウト処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request) // 修正: destroyメソッドの追加
    {
        Auth::logout(); // ユーザーをログアウト

        $request->session()->invalidate(); // セッションを無効化
        $request->session()->regenerateToken(); // CSRFトークンを再生成

        // 修正: 教員ログイン画面にリダイレクト
        return redirect()->route('teacher.login')->with('success', 'ログアウトしました。');
    }

}
