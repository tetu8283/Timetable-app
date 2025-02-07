<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>担当科目一覧</title>
    <link href="{{ asset('css/timetable.css') }}" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-logo">
            <h1>担当科目表示</h1>
        </div>
        <ul class="header-nav">
            <li><a href="{{ route('timetables.index') }}">時間割一覧</a></li>
            @if(Auth::user()->role == 'admin')
                <li><a href="{{ route('users.index') }}">ユーザ一覧</a></li>
                <li><a href="{{ route('staff.timetables.create') }}">時間割作成</a></li>
                <li>
                    <a href="{{ route('staff.timetables.edit', [
                        'year'   => $selectedYear,
                        'month'  => $selectedMonth,
                        'grade'  => $selectedGrade,
                        'course' => $selectedCourse,
                    ]) }}">
                        時間割編集
                    </a>
                </li>
                <li><a href="{{ route('staff.subjects.index') }}">科目作成</a></li>
                <li>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </li>
            @elseif(Auth::user()->role == 'teacher')
                <li><a href="{{ route('staff.timetables.create') }}">時間割作成</a></li>
                <li>
                    <a href="{{ route('staff.timetables.edit', [
                        'year'   => $selectedYear,
                        'month'  => $selectedMonth,
                        'grade'  => $selectedGrade,
                        'course' => $selectedCourse,
                    ]) }}">
                        時間割編集
                    </a>
                </li>
                <li><a href="{{ route('staff.show' , Auth::user()->id) }}">担当科目確認</a></li>
                <li><a href="{{ route('staff.subjects.index') }}">科目作成</a></li>
                <li>
                    <form action="{{ route('teacher.logout') }}" method="POST">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </li>
            @else
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </li>
            @endif
        </ul>
    </header>

    @if(Auth::check())
        <p>ログイン中: {{ Auth::user()->name }} ({{ Auth::user()->role }})</p>
        <p>Email: {{ Auth::user()->email }}</p>
    @endif

    <!-- プルダウンフォーム (年・月・コース・学年) -->
    <form action="{{ route('timetables.index') }}" method="GET" style="display:inline-block; margin-right:10px;">
        <label for="year">年:</label>
        <select name="year" id="year">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endforeach
        </select>

        <label for="month">月:</label>
        <select name="month" id="month">
            @foreach($months as $m)
                <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>
                    {{ $m }}
                </option>
            @endforeach
        </select>

        <label for="course">コース:</label>
        <select name="course" id="course">
            <option value="1" {{ $selectedCourse == 1 ? 'selected' : '' }}>情報システム</option>
            <option value="2" {{ $selectedCourse == 2 ? 'selected' : '' }}>生産管理</option>
            <option value="3" {{ $selectedCourse == 3 ? 'selected' : '' }}>情報セキュリティ</option>
        </select>

        <label for="grade">学年:</label>
        <select name="grade" id="grade">
            @foreach($grades as $g)
                <option value="{{ $g }}" {{ $g == $selectedGrade ? 'selected' : '' }}>
                    {{ $g }}年
                </option>
            @endforeach
        </select>

        <button type="submit">表示</button>
    </form>

    <!-- 時間割テーブル -->
    @if($timetableRecords->isEmpty())
        <p>時間割が存在しません</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>時間割</th>
                    @foreach($weekDays as $dayName)
                        <th>{{ $dayName }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($calendar as $week)
                    <!-- 日付行 -->
                    <tr>
                        <td></td>
                        @foreach($week as $date)
                            <td>
                                @if($date)
                                    {{ $date->format('n/j') }}
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <!-- 各コマ (1〜4) の行 -->
                    @for($period = 1; $period <= $periods; $period++)
                        <tr>
                            <td>{{ $period }}コマ</td>
                            @foreach($week as $date)
                                @if($date)
                                    @php
                                        // 日付とコマからキーを生成してタイムテーブル情報を取得
                                        $key = $date->format('Y-m-d') . '_' . $period;
                                        $timetable = $timetableMap[$key] ?? null;
                                    @endphp

                                    @if($timetable)
                                        @php
                                            $subject = $timetable->subject;
                                        @endphp

                                        {{-- subject が存在し、かつ subject_id が '002' の場合は空セル --}}
                                        @if($subject && $subject->subject_id === '002')
                                            <td></td>
                                        @else
                                            @php
                                                $subjectName = $subject ? $subject->subject_name : '';
                                                $teacherName = $subject ? (optional($subject->user)->name ?? '---') : '';
                                                $location    = $subject ? ($subject->location ?? '未登録') : '';
                                                $bgColor     = $subject ? ($subject->color ?? '#ffffff') : '#ffffff';
                                            @endphp
                                            <td style="background-color: {{ $bgColor }};">
                                                {{ $subjectName }}<br>
                                                {{ $teacherName }}<br>
                                                {{ $location }}
                                            </td>
                                        @endif
                                    @else
                                        <td>(空)</td>
                                    @endif
                                @else
                                    <td></td>
                                @endif
                            @endforeach
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
