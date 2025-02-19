@if($timetableRecords->isEmpty())
    <p>時間割が存在しません</p>
@else
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>時間割</th>
                @foreach($weekDays as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($calendar as $week)
                <!-- 日付行 -->
                <tr>
                    <td></td>
                    @foreach($week as $date)
                        <td>
                            @if($date)
                                {{ $date->format('n/j') }}
                            @endif
                        </td>
                    @endforeach
                </tr>
                <!-- 各コマ (1〜4) の行 -->
                @for($period = 1; $period <= $periods; $period++)
                    <tr>
                        <td>{{ $period }}コマ</td>
                        @foreach($week as $date)
                            @if($date)
                                @php
                                    // 日付とコマからキーを生成してタイムテーブル情報を取得
                                    $key = $date->format('Y-m-d') . '_' . $period;
                                    $timetable = $timetableMap[$key] ?? null;
                                @endphp
                                @if($timetable)
                                    @php
                                        $subject = $timetable->subject;
                                    @endphp
                                    {{-- subject が存在し、かつ subject_id が '002' の場合は空セル --}}
                                    @if($subject && $subject->subject_id === '002')
                                        <td></td>
                                    @else
                                        @php
                                            $subjectName = $subject ? $subject->subject_name : '';
                                            $teacherName = $subject ? (optional($subject->user)->name ?? '---') : '';
                                            $location    = $subject ? ($subject->location ?? '未登録') : '';
                                            $bgColor     = $subject ? ($subject->color ?? '#ffffff') : '#ffffff';
                                        @endphp
                                        <td style="background-color: {{ $bgColor }};">
                                            {{ $subjectName }}<br>
                                            {{ $teacherName }}<br>
                                            {{ $location }}
                                        </td>
                                    @endif
                                @else
                                    <td></td>
                                @endif
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endfor
            @endforeach
        </tbody>
    </table>
@endif
