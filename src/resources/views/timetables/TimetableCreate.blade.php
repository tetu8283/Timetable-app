@extends('layouts.app')

@section('title', '時間割作成')

@push('styles')
    <!-- 独自の CSS ファイル -->
    <link href="{{ asset('css/timetable.css') }}" rel="stylesheet">
    <!-- Select2 用 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')

    <form action="{{ route('staff.timetables.create') }}" method="GET">
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

    <button type="submit" form="storeForm" >
        時間割登録
    </button>

    <br><br>

    <!-- 3) 実際にDBへ保存するためのフォーム (POST) -->
    <form id="storeForm" action="{{ route('staff.timetables.store') }}" method="POST">
        @csrf
        <!-- 選択された年月日・学年・コースを送信 -->
        <input type="hidden" name="year"   value="{{ $selectedYear }}">
        <input type="hidden" name="month"  value="{{ $selectedMonth }}">
        <input type="hidden" name="grade"  value="{{ $selectedGrade }}">
        <input type="hidden" name="course" value="{{ $selectedCourse }}">

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
                            <td class="period">{{ $period }}コマ</td>

                            @foreach ($week as $date)
                                <td class="subject-cell">
                                    @if ($date)
                                        <!-- 科目選択 (Select2) -->
                                        <select name="subjects[{{ $date->format('Y-m-d') }}][{{ $period }}]"
                                                class="subject-select"
                                                style="width: 100%;">
                                            <option value="" data-color="" style="background-color: transparent;">未選択</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                        data-color="{{ $subject->color }}"
                                                        style="background-color: {{ $subject->color }};">
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
@endsection

@push('scripts')
        <!-- jQuery (Select2 の依存ライブラリ) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Select2 の JS (CDN) -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <!-- 独自の JavaScript ファイル -->
        <script src="{{ asset('js/timetable.js') }}"></script>

        {{-- プルダウンの値が変更されたときに、登録用hiddenフィールドに反映する --}}
        <script>
            $(document).ready(function(){
                $('#year, #month, #grade, #course').on('change', function(){
                    var selectedYear   = $('#year').val();
                    var selectedMonth  = $('#month').val();
                    var selectedGrade  = $('#grade').val();
                    var selectedCourse = $('#course').val();

                    // 登録用フォーム内のhiddenフィールドを更新
                    $('#storeForm input[name="year"]').val(selectedYear);
                    $('#storeForm input[name="month"]').val(selectedMonth);
                    $('#storeForm input[name="grade"]').val(selectedGrade);
                    $('#storeForm input[name="course"]').val(selectedCourse);
                });
            });
        </script>
@endpush
