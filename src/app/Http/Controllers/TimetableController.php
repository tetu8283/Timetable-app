<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Subject;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        // 1) プルダウンの選択値を取得 (未指定なら現在の年・月)
        $now = Carbon::now();
        $selectedYear   = $request->input('year',  $now->year);
        $selectedMonth  = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1);

        // 2) プルダウン用データ
        $minYear  = $now->year - 2;
        $maxYear  = $now->year + 2;
        $years    = range($minYear, $maxYear);
        $months   = range(1, 12);
        $grades   = range(1, 4); // 1～4年と仮定

        // 3) カレンダー作成
        $firstDay = Carbon::create($selectedYear, $selectedMonth, 1);
        $lastDay  = $firstDay->copy()->endOfMonth();

        // 月曜～金曜の配列 (必要に応じて日曜～土曜を含めてもOK)
        $weekDays = ['月','火','水','木','金'];

        // カレンダー開始日 (月曜日始まり)
        $startDate = $firstDay->copy()->startOfWeek(Carbon::MONDAY);

        $calendar = []; // 週ごとの配列
        $current  = $startDate->copy();
        while ($current->lte($lastDay)) {
            $week = [];
            for ($i = 0; $i < 5; $i++) {
                $day = $current->copy()->addDays($i);
                // 当月内なら日付をセット、当月外なら null
                $week[] = ($day->month == $firstDay->month) ? $day : null;
            }
            // 週配列に1日でも当月が含まれていれば追加
            if (array_filter($week)) {
                $calendar[] = $week;
            }
            $current->addWeek();
        }

        // 1日あたりのコマ数
        $periods = 4;

        // 4) 今月分のTimetableを一括取得 & 連想配列化
        //    (date + class_period) をキーにして引けるようにする
        $timetableRecords = Timetable::with('subject.user')
            ->where('grade', $selectedGrade)
            ->where('course_id', $selectedCourse)
            ->whereBetween('date', [
                $firstDay->format('Y-m-d'),
                $lastDay->format('Y-m-d')
            ])
            ->get();

        $timetableMap = [];
        foreach ($timetableRecords as $t) {
            $key = $t->date . '_' . $t->class_period; // "YYYY-MM-DD_1" 等
            $timetableMap[$key] = $t;
        }

        // 一番最初のTimetableIDを取得
        $firstTimetableId = $timetableRecords->isEmpty() ? null : $timetableRecords->first()->id;

        // 5) ビューへ
        return view('timetables.TimetableIndex', compact(
            'years','months','grades',
            'selectedYear','selectedMonth','selectedGrade','selectedCourse',
            'weekDays','calendar','periods','timetableMap', 'timetableRecords',
            'firstTimetableId'
        ));
    }

    public function create(Request $request)
    {
        // プルダウンでsubjectを選択するためのデータ取得
        $subjects = Subject::all();
        // 必要に応じてTimetable等のデータ取得処理
        $timetables = Timetable::all();

        // 現在の日時情報
        $now = Carbon::now();

        // リクエストから選択された年・月を取得（なければ現在の年・月）
        $selectedYear  = $request->input('year', $now->year);
        $selectedMonth = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1); // デフォルトは1: 情報システム

        // 選択可能な年（現在の年を基準に前後2年）
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years = range($minYear, $maxYear);

        $months = range(1, 12);
        $grades = range(1, 4);

        // 指定された年・月の初日・最終日を取得
        $firstDayOfMonth = Carbon::create($selectedYear, $selectedMonth, 1);
        $lastDayOfMonth  = $firstDayOfMonth->copy()->endOfMonth();

        // ヘッダー用の曜日（表示は月曜～金曜のみ）
        $weekDays = ['月', '火', '水', '木', '金'];

        // カレンダーの開始日は、指定月の初日の属する週の月曜日に設定
        $startDate = $firstDayOfMonth->copy()->startOfWeek(Carbon::MONDAY);

        $calendar = [];
        $current = $startDate->copy();

        // 指定月の全日程をカバーするまで週ごとに処理する（各週は月曜～金曜の5日分）
        while ($current->lte($lastDayOfMonth)) {
            $week = [];
            // 月曜～金曜（5日分）を作成
            for ($i = 0; $i < 5; $i++) {
                $day = $current->copy()->addDays($i);
                // 当月内の場合のみ日付を設定
                if ($day->month == $firstDayOfMonth->month) {
                    $week[] = $day;
                } else {
                    // 当月外の場合は null をセット（セルを空にするため）
                    $week[] = null;
                }
            }
            // 週に少なくとも1日が含まれている場合のみカレンダーに追加
            if (array_filter($week)) {
                $calendar[] = $week;
            }
            // 1週間分（7日）後の月曜日へ移動
            $current->addWeek();
        }

        // 1日あたりのコマ数
        $periods = 4;

        return view('timetables.TimetableCreate', compact(
            'timetables',
            'calendar',
            'weekDays',
            'periods',
            'years',
            'months',
            'grades',
            'selectedYear',
            'selectedMonth',
            'selectedGrade',
            'selectedCourse',
            'subjects'
        ));
    }

    public function store(Request $request)
    {
        $year   = $request->input('year');
        $month  = $request->input('month');
        $course = $request->input('course');
        $grade  = $request->input('grade');

        // subjects[日付][コマ] => 科目ID
        $subjects = $request->input('subjects', []);

        // バルクインサート用配列
        $insertData = [];

        // subjects[YYYY-MM-DD][コマ] の二次元配列をループ
        foreach ($subjects as $date => $periodArray) {
            foreach ($periodArray as $period => $subjectId) {
                if (empty($subjectId)) {
                    continue;
                }
                // レコード追加
                $insertData[] = [
                    'grade'        => $grade,
                    'course_id'    => $course,
                    'class_period' => $period,
                    'date'         => $date, // "YYYY-MM-DD"
                    'subject_id'   => $subjectId,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ];
            }
        }

        // バルクインサート
        if (!empty($insertData)) {
            Timetable::insert($insertData);
        }

        return redirect()->route('timetables.index')->with('success', '時間割を登録しました');
    }

    public function edit(Request $request)
    {
        $emptySubjectId = 999;

        // 1) プルダウンの選択値を取得
        $now = Carbon::now();
        $selectedYear   = $request->input('year',  $now->year);
        $selectedMonth  = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1);

        // 2) プルダウン用データ
        $minYear  = $now->year - 2;
        $maxYear  = $now->year + 2;
        $years    = range($minYear, $maxYear);
        $months   = range(1, 12);
        $grades   = range(1, 4);

        // 3) カレンダー作成
        $firstDay = Carbon::create($selectedYear, $selectedMonth, 1);
        $lastDay  = $firstDay->copy()->endOfMonth();

        // 月曜～金曜のみ
        $weekDays = ['月','火','水','木','金'];

        $startDate = $firstDay->copy()->startOfWeek(Carbon::MONDAY);
        $calendar  = [];
        $current   = $startDate->copy();
        while ($current->lte($lastDay)) {
            $week = [];
            for ($i=0; $i<5; $i++) {
                $day = $current->copy()->addDays($i);
                $week[] = ($day->month === $firstDay->month) ? $day : null;
            }
            if (array_filter($week)) {
                $calendar[] = $week;
            }
            $current->addWeek();
        }

        $periods = 4; // 1日4コマ

        // 今月分のTimetableを一括取得 (grade, course, date範囲)
        $timetableRecords = Timetable::with('subject') // user情報不要ならsubjectだけでも
            ->where('grade', $selectedGrade)
            ->where('course_id', $selectedCourse)
            ->whereBetween('date', [
                $firstDay->format('Y-m-d'),
                $lastDay->format('Y-m-d')
            ])
            ->get();

        // 連想配列に変換
        $timetableMap = [];
        foreach ($timetableRecords as $t) {
            $key = $t->date . '_' . $t->class_period;
            $timetableMap[$key] = $t;
        }

        // 全科目（選択肢用）
        $subjects = Subject::all();

        return view('timetables.TimetableEdit', compact(
            'years', 'months', 'grades',
            'selectedYear', 'selectedMonth', 'selectedGrade', 'selectedCourse',
            'weekDays', 'calendar', 'periods',
            'timetableMap', 'subjects', 'emptySubjectId'
        ));
    }

    public function update(Request $request)
    {
        // フォームから送信された 年, 月, コース, 学年 を取得
        $year   = $request->input('year');
        $month  = $request->input('month');
        $course = $request->input('course');
        $grade  = $request->input('grade');

        // 「(空)」の科目 ID を取得 (事前に subjects テーブルに "(空)" のレコードを作成)
        $emptySubjectId = Subject::where('subject_name', '(空)')->value('id');

        // `subjects` 配列を取得
        // フォーマット: subjects[YYYY-MM-DD][コマ] => subject_id
        $subjects = $request->input('subjects', []);

        // 各日付 (`YYYY-MM-DD`) をループ処理
        foreach ($subjects as $date => $periodArray) {
            // 各コマ (`1～4` のような番号) をループ処理
            foreach ($periodArray as $period => $subjectId) {
                if (empty($subjectId)) {
                    // もし科目が未選択（空）なら、「(空)」の科目 ID を登録する
                    $subjectId = $emptySubjectId;
                }

                // 科目が選択されている場合、既存のデータを更新 or 新規作成
                Timetable::updateOrCreate(
                    [
                        'grade'         => $grade,         // 学年
                        'course_id'     => $course,       // コースID
                        'date'          => $date,         // 日付 (YYYY-MM-DD)
                        'class_period'  => $period,       // コマ番号 (1～4)
                    ],
                    [
                        'subject_id'    => $subjectId,    // 科目ID (空なら `(空)` の ID)
                        'course_id'     => $course,       // コースID
                        'updated_at'    => now(),        // 更新日時
                    ]
                );
            }
        }

        // すべての処理が完了したら、時間割一覧ページにリダイレクト
        return redirect()->route('timetables.index')->with('success', '時間割を更新しました');
    }

}
