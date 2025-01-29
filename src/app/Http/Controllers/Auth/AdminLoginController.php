<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    /**
     * 管理者ログイン画面を表示
     */
    public function create()
    {
        return view('auth.AdminLogin'); // 管理者ログインビュー
    }

    /**
     * 管理者ログイン処理
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        // ログインしたユーザーが admin かどうかチェック
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'school_id' => '管理者としてログインできません。',
            ]);
        }

        return redirect()->route('timetables.index');
    }

    /**
     * 管理者ログアウト処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout(); // ユーザーをログアウト

        $request->session()->invalidate(); // セッションを無効化
        $request->session()->regenerateToken(); // CSRFトークンを再生成

        return redirect()->route('admin.login')->with('success', 'ログアウトしました。');
    }
}
