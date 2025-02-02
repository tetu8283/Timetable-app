<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>科目一覧</title>
</head>
<body>
    <a href="{{ route('timetables.index') }}">時間割一覧</a>
    <a href="{{ route('timetables.create') }}">時間割作成</a>
    <div class="create-subject">
        <form action="{{ route('subject.store') }}" method="POST">
            @csrf

            <label for="subject_code">科目番号:</label>
            <input type="text" id="subject_code" name="subject_code" required>
            <label for="subject_name">科目名:</label>
            <input type="text" id="subject_name" name="subject_name" required>
            <label for="school_id">担当者番号:</label>
            <input type="text" id="school_id" name="school_id" required>
            <label for="color">背景色:</label>
            <input type="color" id="color" name="color" value="#ffffff">

            <button type="submit">作成</button>
        </form>

        <!-- バリデーションエラーメッセージの表示 -->
        @if ($errors->any())
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="all-subject">
        <p>科目一覧</p>
    <table>
        <tr>
            <th>科目番号</th>
            <th>科目名</th>
            <th>学籍番号</th>
            <th>背景色</th>
        </tr>
        @foreach ($subjects as $subject)
            <tr>
                <td>{{ $subject->subject_code}}</td>
                <td>{{ $subject->subject_name }}</td>
                <td>{{ $subject->school_id }}</td>
                <td style="background-color: {{ $subject->color }};"></td>
                @if(Auth::user()->role === 'admin')
                    <td>
                        <a href="{{ route('subject.edit', $subject->id) }}">編集</a>
                    </td>
                    <td>
                        <form action="{{ route('subject.destroy', $subject->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('削除してよろいしいですか？')">削除</button>
                        </form>
                    </td>
                @endif
            </tr>
        @endforeach
    </table>
    </div>
</body>
</html>
