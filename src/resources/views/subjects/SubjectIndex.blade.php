@extends('layouts.app')

@section('title', '科目作成')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/subjects/index.css') }}">
@endpush

@section('content')
    <div class="subject-container">
        <div class="pulldown-container">
            <form action="{{ route('staff.subjects.store') }}" method="POST">
                @csrf

                <label for="subject_id">科目番号:</label>
                <input type="text" id="subject_id" name="subject_id" required>
                <label for="subject_name">科目名:</label>
                <input type="text" id="subject_name" name="subject_name" required>
                <label for="school_id">学籍番号:</label>
                <input type="text" id="school_id" name="school_id" required>
                <label for="location">場所:</label>
                <input type="text" id="location" name="location">
                <label for="color">背景色:</label>
                <input class="color" type="color" id="color" name="color" value="#ffffff">

                <button type="submit">作成</button>
            </form>
        </div>

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

        <div class="index-subject">
            <p>科目一覧</p>
            <table>
                <tr>
                    <th>科目番号</th>
                    <th>科目名</th>
                    <th>学籍番号</th>
                    <th>場所</th>
                    <th>背景色</th>
                    @if(Auth::user()->role === 'admin')
                        <th></th>
                        <th></th>
                    @endif
                </tr>
                @foreach ($subjects as $subject)
                    <tr>
                        <td>{{ $subject->subject_id}}</td>
                        <td>{{ $subject->subject_name }}</td>
                        <td>{{ $subject->school_id }}</td>
                        <td>{{ $subject->location }}</td>
                        <td style="background-color: {{ $subject->color }};"></td>
                        @if(Auth::user()->role === 'admin')
                            <td>
                                <a href="{{ route('staff.subjects.edit', $subject->id) }}">編集</a>
                            </td>
                            <td>
                                <form action="{{ route('staff.subjects.destroy', $subject->id) }}" method="POST">
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
    </div>
@endsection

@push('scripts')

@endpush
