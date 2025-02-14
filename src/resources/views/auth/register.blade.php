<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
</head>
<body>
    <div class="register-container">
        <h2 class="logo">登録</h2>

        <form method="POST" action="{{ route('register') }}" class="register-form">
        @csrf

        <div class="form-group">
            <input id="school_id" class="input-school-id" type="text" name="school_id" value="{{ old('school_id') }}" placeholder="学籍番号" required autofocus autocomplete="school_id">
            @error('school_id')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <input id="name" class="input-name" type="text" name="name" value="{{ old('name') }}" placeholder="氏名" required autocomplete="name">
            @error('name')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <input id="email" class="input-email" type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" required autocomplete="username">
            @error('email')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <input id="password" class="input-password" type="password" name="password" placeholder="パスワード" required autocomplete="new-password">
            @error('password')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <input id="password_confirmation" class="input-password-confirmation" type="password" name="password_confirmation" placeholder="パスワード確認" required autocomplete="new-password">
            @error('password_confirmation')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group form-actions">
            <a class="already-registered" href="{{ route('login') }}">
            Already registered?
            </a>
        </div>

        <div class="form-group">
            <button type="submit" class="register-button">Register</button>
        </div>
        </form>
    </div>
</body>
</html>
