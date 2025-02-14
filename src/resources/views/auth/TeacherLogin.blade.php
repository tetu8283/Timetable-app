<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>教員ログイン画面</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h2 class="logo">教員ログイン画面</h2>

        <!-- Session Status -->
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('teacher.login.store') }}" class="login-form">
            @csrf

            <div class="form-group">
                <input class="input-school-id" type="text" name="school_id" value="{{ old('school_id') }}" placeholder="学籍番号" required autofocus autocomplete="school_id">
                @error('school_id')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <input class="input-password" type="password" name="password" placeholder="パスワード" required autocomplete="current-password">
                @error('password')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                @if (Route::has('password.request'))
                <a class="forgot-password" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif
            </div>

            <div class="form-group">
                <button type="submit" class="login-button">
                {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</body>
</html>
