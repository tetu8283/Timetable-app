<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;
use Carbon\Carbon;


class TimetableController extends Controller
{
    // public function index()
    // {
    //     $timetables = Timetable::all();
    //     $startOfWeek = Carbon::now()->startOfWeek(); // 今週の月曜日の日付を取得
    //     $dates = [];

    //     $numberOfDay = 0;
    //     $weekOfDays = 5;
    //     $weeks = ['(月)', '(火)', '(水)', '(木)', '(金)'];

    //     for ($numberOfDay; $numberOfDay < $weekOfDays; $numberOfDay++) {
    //         $dates[] = $startOfWeek->copy()->addDays($numberOfDay);
    //     }
    //     return view('timetables.TimetableIndex', compact('timetables', 'dates', 'weeks'));
    // }

    public function index(Request $request)
    {
        // 学年とコースの選択
        $selectedGrade = $request->input('grade', 1);  // デフォルト1年
        $selectedCourse = $request->input('course', 1); // デフォルトコース1

        // 表示形式（日別・週別・月別）
        $viewType = $request->input('view_type', 'week'); // デフォルト週別

        // 今日の日付など
        $today = now()->startOfDay();

        // 表示範囲を計算
        switch($viewType) {
            case 'day':
                // もし特定の日を指定したい場合は、さらに date パラメータを受け取って指定
                // 例: ?view_type=day&date=2025-02-10
                $targetDate = $request->input('date', $today->format('Y-m-d'));
                $startDate = $targetDate;
                $endDate = $targetDate;
                break;

            case 'month':
                // 月初と月末を計算
                $year = $request->input('year', $today->format('Y'));
                $month = $request->input('month', $today->format('m'));
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
                break;

            default:
            case 'week':
                // 週の場合 (月〜金 or 日〜土)
                // 今回は月〜金として例示
                $startDateInput = $request->input('start');
                if ($startDateInput) {
                    $startDate = Carbon::parse($startDateInput)->startOfDay();
                } else {
                    // デフォルトは「今週の月曜日」
                    $startDate = $today->copy()->startOfWeek();
                }
                // 月〜金までなら+4日
                $endDate = $startDate->copy()->addDays(4)->endOfDay();
                break;
        }

        // timetablesテーブルから該当期間・学年・コースを検索
        $timetables = Timetable::where('grade', $selectedGrade)
                        ->where('course', $selectedCourse)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date')
                        ->orderBy('class_period')
                        ->get();

        // Bladeに渡す
        return view('timetables.index', [
            'timetables' => $timetables,
            'selectedGrade' => $selectedGrade,
            'selectedCourse' => $selectedCourse,
            'viewType' => $viewType,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }


    public function create()
    {
        return view('timetables.TimetableCreate');
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
