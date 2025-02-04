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
    <a href="{{ route('timetables.index') }}">時間割一覧</a> |
    <a href="{{ route('staff.subjects.index') }}">科目作成</a>

    <!-- 1) 表示切り替え用フォーム (GET) -->
    <!--    横並びにしたいので、display:inline-block を付与 -->
    <form action="{{ route('staff.timetables.edit') }}" method="GET">
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

        <label for="grade">学年:</label>
        <select name="grade" id="grade">
            @foreach ($grades as $g)
                <option value="{{ $g }}" {{ $g == $selectedGrade ? 'selected' : '' }}>
                    {{ $g }}年
                </option>
            @endforeach
        </select>

        <button type="submit">表示</button>
    </form>

    <button type="submit" form="updateForm" >
        時間割更新
    </button>

    <br><br>

    <!-- 3) 実際にDBへ保存するためのフォーム (POST) -->
    <form id="updateForm" action="{{ route('staff.timetables.update') }}" method="POST">
        @csrf
        <!-- 選択された年月日・学年・コースを送信 -->
        <input type="hidden" name="year"   value="{{ $selectedYear }}">
        <input type="hidden" name="month"  value="{{ $selectedMonth }}">
        <input type="hidden" name="grade"  value="{{ $selectedGrade }}">
        <input type="hidden" name="course" value="{{ $selectedCourse }}">

        <!-- 時間割表 -->
        <table class="timetable">
            <thead>
                <tr>
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

                    <!-- 各コマの行 (1～4コマ) -->
                    @for ($period = 1; $period <= $periods; $period++)
                        <tr>
                            <!-- コマ番号 -->
                            <td class="period">{{ $period }}コマ</td>

                            @foreach ($week as $date)
                                <td class="subject-cell">
                                    @if ($date)
                                        @php
                                            // "YYYY-MM-DD_コマ" で既存のtimetableレコードを探す
                                            $key = $date->format('Y-m-d').'_'.$period;
                                            $t = $timetableMap[$key] ?? null;
                                            // 既存の subject_id があれば
                                            $currentSubjectId = $t ? $t->subject_id : null;
                                        @endphp

                                        <!-- 科目選択 (Select2) -->
                                        <select name="subjects[{{ $date->format('Y-m-d') }}][{{ $period }}]"
                                            class="subject-select"
                                            style="width: 100%;">
                                        <option value="{{ $emptySubjectId }}" data-color="" style="background-color: transparent;"
                                            @if($currentSubjectId == $emptySubjectId) selected @endif>
                                            (空)
                                        </option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                    data-color="{{ $subject->color }}"
                                                    style="background-color: {{ $subject->color }};"
                                                    @if($subject->id == $currentSubjectId) selected @endif>
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                        </select>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                @endforeach
            </tbody>
        </table>
    </form>

    <!-- jQuery (Select2 の依存ライブラリ) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 の JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- 独自の JavaScript ファイル -->
    <script src="{{ asset('js/timetable.js') }}"></script>
</body>
</html>
