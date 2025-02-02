<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>時間割作成</title>
    <!-- 独自の CSS ファイル -->
    <link href="{{ asset('css/timetable.css') }}" rel="stylesheet">
    <!-- Select2 用 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <a href="{{ route('timetables.index') }}">時間割一覧</a>
    <a href="{{ route('subject.index') }}">科目作成</a>

    <!-- 年・月選択フォーム -->
    <form action="{{ route('timetables.create') }}" method="GET">
        <label for="year">年:</label>
        <select name="year" id="year">
            @foreach ($years as $year)
                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach
        </select>

        <label for="month">月:</label>
        <select name="month" id="month">
            @foreach ($months as $month)
                <option value="{{ $month }}" {{ $month == $selectedMonth ? 'selected' : '' }}>
                    {{ $month }}
                </option>
            @endforeach
        </select>

        <label for="course">コース:</label>
        <select name="course" id="course">
            <option value="1" {{ $selectedCourse == 1 ? 'selected' : '' }}>情報システム</option>
            <option value="2" {{ $selectedCourse == 2 ? 'selected' : '' }}>生産管理</option>
            <option value="3" {{ $selectedCourse == 3 ? 'selected' : '' }}>情報セキュリティ</option>
        </select>

        <button type="submit">表示</button>
    </form>

    <br>

    <!-- 時間割表 -->
    <table class="timetable">
        <thead>
            <tr>
                <!-- 左上セル：タイトル -->
                <th>時間割</th>
                @foreach ($weekDays as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($calendar as $week)
                <!-- 日付行 -->
                <tr>
                    <td></td>
                    @foreach ($week as $date)
                        <td class="date-cell">
                            @if ($date)
                                <div>{{ $date->format('n/j') }}</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <!-- 各コマの行 -->
                @for ($period = 1; $period <= $periods; $period++)
                    <tr>
                        <!-- コマ番号 -->
                        <td class="period">{{ $period }}コマ</td>
                        @foreach ($week as $date)
                            <td class="subject-cell">
                                <!-- 科目選択フォーム（Select2 を使用） -->
                                <select name="subject" class="subject-select" style="width: 100%;">
                                    <option value="" data-color="" style="background-color: transparent;">未選択</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                                data-color="{{ $subject->color }}"
                                                style="background-color: {{ $subject->color }};">
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        @endforeach
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>

    <!-- jQuery (Select2 の依存ライブラリ) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 の JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- 独自の JavaScript ファイル -->
    <script src="{{ asset('js/timetable.js') }}"></script>
</body>
</html>
