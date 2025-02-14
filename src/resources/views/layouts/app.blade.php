<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'デフォルトタイトル')</title>

    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    @stack('styles')
</head>
<body>
    {{-- 共通ヘッダーを読み込む --}}
    @include('partials.header')

    <main>
        {{-- 各ページごとのコンテンツ --}}
        @yield('content')
    </main>

    <footer>
        {{-- 共通フッターなどがあればここに記述 --}}
    </footer>

    {{-- ページ固有のスクリプトがあれば --}}
    @stack('scripts')
    {{-- header用のjsファイル --}}
    <script src="{{ asset('js/header.js') }}"></script>
</body>
</html>
