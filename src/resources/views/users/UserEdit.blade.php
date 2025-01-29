<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width= , initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>プロフィール編集画面</title>
</head>
<body>
    <header>
        <a class="to-timetable-index" href="{{ route('timetables.index') }}">時間割一覧</a>
        <p>プロフィール編集画面</p>
    </header>
    <main>
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <label for="name">名前</label>
            <input type="text" name="name" value="{{ $user->name }}">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ $user->email }}">
            <button type="submit">更新</button>
        </form>
    </main>
</body>
</html>
