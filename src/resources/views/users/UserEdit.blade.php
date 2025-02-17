@extends('layouts.app')

@section('title', 'プロフィール編集画面')

@push('styles')
    <link href="{{ asset('css/users/edit.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="edit-container">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <input class="input-name" type="text" name="name" value="{{ $user->name }}" placeholder="氏名" required>
            </div>
            <div class="form-group">
                <input class="input-email" type="email" name="email" value="{{ $user->email }}" placeholder="メールアドレス" required>
            </div>
            <div class="form-group">
                <button type="submit">更新</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')

@endpush
