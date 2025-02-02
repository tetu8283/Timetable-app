<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Subject;


class TimetableController extends Controller
{
    public function index()
    {
        $timetables = Timetable::all();
        $startOfWeek = Carbon::now()->startOfWeek(); // 今週の月曜日の日付を取得
        $dates = [];

        $numberOfDay = 0;
        $weekOfDays = 5;
        $weeks = ['(月)', '(火)', '(水)', '(木)', '(金)'];

        for ($numberOfDay; $numberOfDay < $weekOfDays; $numberOfDay++) {
            $dates[] = $startOfWeek->copy()->addDays($numberOfDay);
        }
        return view('timetables.TimetableIndex', compact('timetables', 'dates', 'weeks'));
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
        $selectedCourse = $request->input('course', 1); // デフォルトは1: 情報システム

        // 選択可能な年（現在の年を基準に前後2年）
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years = range($minYear, $maxYear);

        // 選択可能な月（1～12月）
        $months = range(1, 12);

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
            'selectedYear',
            'selectedMonth',
            'selectedCourse',
            'subjects'
        ));
    }

    public function store(Request $request)
    {

    }

    public function edit(Timetable $timetable)
    {

    }

    public function update(Request $request, Timetable $timetable)
    {

    }

}
