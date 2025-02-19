@extends('layouts.app')

@section('title', '科目更新')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/subjects/edit.css') }}">
@endpush

@section('content')
    <div class="edit-container">
        <form action="{{ route('staff.subjects.update', $subject->id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="text" class="input-subject-name" name="subject_name" value="{{ $subject->subject_name }}" placeholder="科目名" required>
            <input type="text" class="input-school-id" name="school_id" value="{{ $subject->school_id }}" placeholder="学籍番号" required>
            <input type="text" class="input-location" name="location" value="{{ $subject->location }}" placeholder="場所">
            <input type="color" class="input-color" name="color" value="{{ $subject->color }}">

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
