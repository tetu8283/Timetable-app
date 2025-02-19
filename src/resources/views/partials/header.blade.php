<header>
    <div class="header-logo">
        <a href="{{ route('timetables.index') }}" style="text-decoration: none; color: black;">
            <h1>{{ $headerTitle  }}</h1>
        </a>
    </div>

    <div id="js-toggle-sidebar">&equiv;</div>
    <nav class="header-nav" id="js-nav">
        <ul class="nav-items">
            <li class="nav-item"><a href="{{ route('users.edit', Auth::user()->id) }}">プロフィール編集</a></li>
            <li class="nav-item"><a href="{{ route('timetables.index') }}">時間割一覧</a></li>
            @if(Auth::check())
                @if(Auth::user()->role == 'admin')
                    <li class="nav-item"><a href="{{ route('users.index') }}">ユーザ一覧</a></li>
                    <li class="nav-item"><a href="{{ route('staff.timetables.create') }}">時間割作成</a></li>
                    <li class="nav-item">
                        <a href="{{ route('staff.timetables.edit', [
                            'year'   => $selectedYear ?? '',
                            'month'  => $selectedMonth ?? '',
                            'grade'  => $selectedGrade ?? '',
                            'course' => $selectedCourse ?? '',
                        ]) }}">
                            時間割編集
                        </a>
                    </li>
                    <li class="nav-item"><a href="{{ route('staff.show', Auth::user()->id) }}">担当科目確認</a></li>
                    <li class="nav-item"><a href="{{ route('staff.subjects.index') }}">科目作成</a></li>
                    <li class="nav-item">
                        <form class="" action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button class="logout-button" type="submit">ログアウト</button>
                        </form>
                    </li>
                @elseif(Auth::user()->role == 'teacher')
                    <li class="nav-item"><a href="{{ route('staff.timetables.create') }}">時間割作成</a></li>
                    <li class="nav-item">
                        <a href="{{ route('staff.timetables.edit', [
                            'year'   => $selectedYear ?? '',
                            'month'  => $selectedMonth ?? '',
                            'grade'  => $selectedGrade ?? '',
                            'course' => $selectedCourse ?? '',
                        ]) }}">
                            時間割編集
                        </a>
                    </li>
                    <li class="nav-item"><a href="{{ route('staff.show', Auth::user()->id) }}">担当科目確認</a></li>
                    <li class="nav-item"><a href="{{ route('staff.subjects.index') }}">科目作成</a></li>
                    <li class="nav-item">
                        <form action="{{ route('teacher.logout') }}" method="POST">
                            @csrf
                            <button class="logout-button" type="submit">ログアウト</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="logout-button" type="submit">ログアウト</button>
                        </form>
                    </li>
                @endif
            @endif
        </ul>
    </nav>
</header>
