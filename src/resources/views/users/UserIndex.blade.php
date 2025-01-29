<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ユーザ一覧</title>
</head>
<body>
    <header>
        <a class="to-timetable-index" href="{{ route('timetables.index') }}">時間割一覧</a>
        <p>ユーザ一覧画面</p>
    </header>
    <main>
        <table border="1px">
            <thead>
                <tr>
                    <th>学籍番号</th>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>役割</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->school_id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        @if($user->id == Auth::id())
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}">編集</a>
                            </td>
                        @endif

                        @if(Auth::user()->role === 'admin')
                            <td>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick='deleteAlert()'>削除</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>

    <script src="{{ asset('js/message.js') }}"></script>
</body>
</html>
