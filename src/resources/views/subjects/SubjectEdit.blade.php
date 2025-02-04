<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>科目編集</title>
</head>
<body>
    <a href="{{ route('timetables.index') }}">時間割一覧</a>
    <a href="{{ route('staff.timetables.create') }}">時間割作成</a>
    <div class="edit-subject">
        <form action="{{ route('staff.subjects.update', $subject->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="subject_name">科目名:</label>
            <input type="text" id="subject_name" name="subject_name" value="{{ $subject->subject_name }}" required>
            <label for="school_id">担当者番号:</label>
            <input type="text" id="school_id" name="school_id" value="{{ $subject->school_id }}" required>
            <label for="location">場所:</label>
            <input type="text" id="location" name="location" value="{{ $subject->location }}" required>
            <label for="color">背景色:</label>
            <input type="color" id="color" name="color" value="{{ $subject->color }}">

            <button type="submit">更新</button>
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
</body>
</html>
