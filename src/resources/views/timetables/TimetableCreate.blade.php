<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>時間割作成</title>
</head>
<body>
    <a href="{{ route('timetables.index') }}">時間割一覧</a>
    @if(Auth::user()->role === 'teacher' or Auth::user()->role === 'admin')
        <a href="{{ route('subject.index') }}">科目作成</a>
    @endif


    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <!-- 左上のセルは空（または「時間割」等のタイトル） -->
                <th>時間割</th>
                @foreach ($weekDays as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- 各週ごとに、1日を4コマに分割して表示 -->
            @foreach ($calendar as $week)
                <!-- 日付を表示する行 -->
                <tr>
                    <td></td>
                    @foreach ($week as $date)
                    <td style="width:140px; height:25px; vertical-align:top; text-align:center;">
                        @if ($date)
                        <div>{{ $date->format('n/j') }}</div>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @for ($period = 1; $period <= $periods; $period++)
                    <tr>
                    <!-- 行の最初のセルに何コマ目かを表示 -->
                    <td>{{ $period }}コマ</td>
                    <!-- 各曜日ごとのセル -->
                    @foreach ($week as $date)
                        <td style="width:140px; height:40px; vertical-align:top; text-align:center;">
                        <!-- ここに各コマの授業内容やフォームなどを配置できます -->
                        </td>
                    @endforeach
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>


</body>
</html>

