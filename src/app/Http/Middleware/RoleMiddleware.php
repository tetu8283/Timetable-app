<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles  // 可変長引数で許可するロールを受け取る
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ユーザーがログインしていない場合はログイン画面へリダイレクト
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 現在ログインしているユーザーを取得
        $user = Auth::user();

        // 引数で受け取ったロールの中にユーザーのロールが存在するかチェック
        if (! in_array($user->role, $roles)) {
            // ロールが一致しない場合は 403 やトップページなどにリダイレクト
            abort(403, 'Unauthorized action.');
        }

        // 次の処理に進む
        return $next($request);
    }
}
