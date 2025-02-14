@extends('layouts.app')

@section('title', '科目更新')

@push('styles')
    <!-- 独自の CSS ファイル -->
@endpush

@section('content')
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
@endsection

@push('scripts')
    <!-- 独自の JavaScript ファイル -->
@endpush
