<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ログイン画面</title>
  <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
    <div class="login-container">
        <h2 class="logo">ログイン画面</h2>

        <!-- Session Status -->
        @if (session('status'))
        <div class="status">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        <div class="form-group">
            <input id="school_id" class="input-school-id" type="text" name="school_id" value="{{ old('school_id') }}" placeholder="学籍番号" required autofocus autocomplete="school_id">
            @error('school_id')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <input id="password" class="input-password" type="password" name="password" placeholder="パスワード" required autocomplete="current-password">
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-group">
            <label for="remember_me" class="remember-me-label">
            <input id="remember_me" type="checkbox" name="remember">
            <span>{{ __('Remember me') }}</span>
            </label>
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
