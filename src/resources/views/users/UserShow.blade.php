@extends('layouts.app')

@section('title', '担当科目確認')

@push('styles')
    <!-- 独自の CSS ファイル -->
    <link href="{{ asset('css/users/show.css') }}" rel="stylesheet">
    <!-- Select2 用 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <div class="show-container">
        <div class="pulldown-container">
            <!-- プルダウンフォーム（年・月・コース・学年・表示形式） -->
            <form action="{{ route('staff.show', Auth::user()->id) }}" method="GET" style="display:inline-block; margin-right:10px;" id="timetable-form">
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

                <label for="viewMode">表示形式:</label>

                <button type="submit">表示</button>
            </form>
        </div>
        <!-- タイムテーブル表示部分（部分ビューをインクルード） -->
        <div id="timetable-content">
            @include('timetables.partials.TimeTable')
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery (Select2 の依存ライブラリ) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 の JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- 独自の JavaScript ファイル -->
    <script src="{{ asset('js/timetable.js') }}"></script>
@endpush
