<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>時間割一覧</title>
</head>
<body>
    <p>時間割一覧</p>
    @if(Auth::user()->role === 'teacher')
        <form action="{{ route('teacher.logout') }}" method="POST">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    @elseif(Auth::user()->role === 'admin')
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
        <a href="{{ route('users.index') }}">ユーザ一覧</a>
    @else
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    @endif

    @if(Auth::user()->role === 'teacher' or Auth::user()->role === 'admin')
        <a href="{{ route('timetables.create') }}">時間割作成</a>
    @endif

    <p>私は{{ Auth::user()->role }}です</p>

    <table border="1">
        <thead>
            <tr>
                <th></th>
                @foreach ($dates as $date)
                    <th>{{ $date }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 4; $i++)
                <tr>
                    <td>{{ $i }}コマ目</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="profile">
        <p class="name">名前: {{ Auth::user()->name }}</p>
        <p class="email">メール: {{ Auth::user()->email }}</p>
        <p class="role">役割: {{ Auth::user()->role }}</p>

        <p>
            <a href="{{ route('users.edit', Auth::user()->id) }}">編集</a>
        </p>
    </div>
</body>
</html>
