@extends('layouts.app')

@section('title', 'ユーザ一覧')

@push('styles')
    <!-- 独自の CSS ファイル -->
    <link rel="stylesheet" href="{{ asset('css/UserIndex.css') }}">
@endpush

@section('content')
    @if(@session('success'))
        <div class="flash-msg">
            {{ session('success') }}
        </div>
    @endif

    <table border="1px">
        <thead>
            <tr>
                <th>学籍番号</th>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>役割</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->school_id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    @if($user->id == Auth::id())
                        <td>
                            <a href="{{ route('users.edit', $user->id) }}">編集</a>
                        </td>
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <td>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="delete-button"
                                    onclick="return confirm('削除してよろしいですか？');">
                                    削除
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')

@endpush
