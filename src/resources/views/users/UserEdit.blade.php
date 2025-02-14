@extends('layouts.app')

@section('title', 'プロフィール編集画面')

@push('styles')
    <!-- 独自の CSS ファイル -->
@endpush

@section('content')
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="name">名前</label>
        <input type="text" name="name" value="{{ $user->name }}" required>
        <label for="email">メールアドレス</label>
        <input type="email" name="email" value="{{ $user->email }}" required>
        <button type="submit">更新</button>
    </form>
@endsection

@push('scripts')

@endpush
