<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\Timetable;
use App\Services\TimetableService;

class TimetableController extends Controller
{
    protected $timetableService;

    /**
     * コンストラクタ
     *
     * @param \App\Services\TimetableService $timetableService
     */
    public function __construct(TimetableService $timetableService)
    {
        $this->timetableService = $timetableService;
    }

    public function index(Request $request)
    {
        $now            = Carbon::now();
        $selectedYear   = $request->input('year',  $now->year);
        $selectedMonth  = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1);
        $viewMode       = $request->input('view', 'month'); // 表示モード：'week', 'month' または 'quarter'

        // 新たに週オフセットのパラメータを取得（初期値は 0：現在の週）
        $weekOffset = (int) $request->input('week_offset', 0);

        // プルダウン用の値
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years   = range($minYear, $maxYear);
        $months  = range(1, 12);
        $grades  = range(1, 4);
        $weekDays = ['月','火','水','木','金'];
        $periods  = 4; // 1日あたりのコマ数

        if ($viewMode === 'week') {
            // 週別表示の場合
            // 基準となる日付を決定
            if ($selectedYear == $now->year && $selectedMonth == $now->month) {
                $selectedDate = $now->copy();
            } else {
                $selectedDate = Carbon::create($selectedYear, $selectedMonth, 15);
            }
            // 週オフセットを加える（前週なら -1、翌週なら +1 など）
            $selectedDate->addWeeks($weekOffset);

            $startOfWeek = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
            $week = [];
            for ($i = 0; $i < 5; $i++) {
                $week[] = $startOfWeek->copy()->addDays($i);
            }
            $calendar = [$week];
            $firstDay = $week[0];
            $lastDay  = $week[4];
        } elseif ($viewMode === 'quarter') {
            // 四半期表示の場合
            $calendarData = $this->timetableService->getQuarterCalendar($selectedYear, $selectedMonth);
            $calendar     = $calendarData['calendar'];
            $firstDay     = $calendarData['firstDay'];
            $lastDay      = $calendarData['lastDay'];
        } else {
            // 月別表示の場合
            $calendarData = $this->timetableService->getCalendar($selectedYear, $selectedMonth);
            $calendar     = $calendarData['calendar'];
            $firstDay     = $calendarData['firstDay'];
            $lastDay      = $calendarData['lastDay'];
        }

        // 指定期間のTimetableを一括取得＆連想配列化
        $timetableData = $this->timetableService->getTimetableMap($selectedGrade, $selectedCourse, $firstDay, $lastDay);
        extract($timetableData);

        $headerTitle = '時間割一覧';

        // 週表示の場合、前週・翌週用のオフセット値も渡す
        $prevWeekOffset = $weekOffset - 1;
        $nextWeekOffset = $weekOffset + 1;

        return view('timetables.TimetableIndex', compact(
            'headerTitle', 'years', 'months', 'grades',
            'selectedYear', 'selectedMonth', 'selectedGrade', 'selectedCourse',
            'weekDays', 'calendar', 'periods', 'timetableMap', 'timetableRecords',
            'firstTimetableId', 'viewMode', 'weekOffset', 'prevWeekOffset', 'nextWeekOffset'
        ));
    }



    /**
     * 時間割作成ページ
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $now            = Carbon::now();
        $selectedYear   = $request->input('year', $now->year);
        $selectedMonth  = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1);

        // プルダウン用の値
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years   = range($minYear, $maxYear);
        $months  = range(1, 12);
        $grades  = range(1, 4);
        $weekDays = ['月', '火', '水', '木', '金'];
        $periods  = 4;

        // create用カレンダー作成（必要に応じて当月外は null をセット）
        $calendarData = $this->timetableService->getCalendar($selectedYear, $selectedMonth);
        $calendar     = $calendarData['calendar'];

        // 全件取得（必要に応じてサービス側に移すことも可能）
        $timetables   = Timetable::all();
        $subjects     = Subject::all();

        $headerTitle = '時間割作成'; // ヘッダータイトル（時間割作成ページ）

        return view('timetables.TimetableCreate', compact(
            'headerTitle',
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

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $grade  = $request->input('grade');
        $course = $request->input('course');
        // subjects[YYYY-MM-DD][コマ] => 科目ID の配列
        $subjects = $request->input('subjects', []);

        // 選択された年と月を取得（隠しフィールドから送信されている）
        $selectedYear  = $request->input('year');
        $selectedMonth = $request->input('month');

        $this->timetableService->bulkInsertTimetables($selectedYear, $selectedMonth, $grade, $course, $subjects);

        // リダイレクト時に選択された年月・学年・コースをクエリパラメータとして渡す
        return redirect()->route('timetables.index', [
            'year'   => $selectedYear,
            'month'  => $selectedMonth,
            'grade'  => $grade,
            'course' => $course,
        ])->with('success', '時間割を登録しました');
    }

    /**
     * 時間割更新ページ
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request)
    {
        $emptySubjectId = 999; // ビュー側で使用

        $now            = Carbon::now();
        $selectedYear   = $request->input('year',  $now->year);
        $selectedMonth  = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1);

        // プルダウン用データ
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years   = range($minYear, $maxYear);
        $months  = range(1, 12);
        $grades  = range(1, 4);
        $weekDays = ['月','火','水','木','金'];
        $periods  = 4;

        // カレンダー作成
        $calendarData = $this->timetableService->getCalendar($selectedYear, $selectedMonth);
        $calendar     = $calendarData['calendar'];
        $firstDay     = $calendarData['firstDay'];
        $lastDay      = $calendarData['lastDay'];

        // 今月分のTimetableを一括取得 & 連想配列化
        $timetableData = $this->timetableService->getTimetableMap($selectedGrade, $selectedCourse, $firstDay, $lastDay);
        extract($timetableData); // $timetableRecords, $timetableMap など

        // 全科目（選択肢用）
        $subjects = Subject::all();

        $headerTitle = '時間割更新'; // ヘッダータイトル（時間割更新ページ）

        return view('timetables.TimetableEdit', compact(
            'headerTitle',
            'years', 'months', 'grades',
            'selectedYear', 'selectedMonth', 'selectedGrade', 'selectedCourse',
            'weekDays', 'calendar', 'periods',
            'timetableMap', 'subjects', 'emptySubjectId'
        ));
    }

    /**
     * 時間割更新処理
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $grade  = $request->input('grade');
        $course = $request->input('course');
        $selectedYear  = $request->input('year');
        $selectedMonth = $request->input('month');

        // フォームで選択した科目を配列で取得
        $subjects = $request->input('subjects', []);

        $this->timetableService->updateTimetables($grade, $course, $subjects);

        return redirect()->route('timetables.index', [
            'year'   => $selectedYear,
            'month'  => $selectedMonth,
            'grade'  => $grade,
            'course' => $course,
        ])->with('success', '時間割を更新しました');
    }

    /**
     * ユーザ詳細ページ
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Request $request)
    {
        // ログインユーザー情報取得
        $user = Auth::user();
        $school_id = $user->school_id;

        $now            = Carbon::now();
        $selectedYear   = $request->input('year',  $now->year);
        $selectedMonth  = $request->input('month', $now->month);
        $selectedGrade  = $request->input('grade', 1);
        $selectedCourse = $request->input('course', 1);

        // プルダウン用の値
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years   = range($minYear, $maxYear);
        $months  = range(1, 12);
        $grades  = range(1, 4);
        $weekDays = ['月','火','水','木','金'];
        $periods  = 4; // 1日あたりのコマ数

        // カレンダー作成（初日・最終日を含む）
        $calendarData = $this->timetableService->getCalendar($selectedYear, $selectedMonth);
        $calendar     = $calendarData['calendar'];
        $firstDay     = $calendarData['firstDay'];
        $lastDay      = $calendarData['lastDay'];

        // 今月分の Timetable を一括取得し、連想配列に変換
        $timetableData = $this->timetableService->getTimetableMap($selectedGrade, $selectedCourse, $firstDay, $lastDay);
        extract($timetableData); // $timetableRecords, $timetableMap, $firstTimetableId が利用可能

        // フォールバック用の Subject を取得（存在しなければ作成）
        $fallbackSubject = Subject::updateOrCreate(
            ['subject_id' => '002'],
            [
                'subject_name' => ' ',
                'school_id'    => 'admin001',
                'color'        => '#ffffff',
                'location'     => ' '
            ]
        );

        // 各 Timetable の Subject の school_id がログインユーザーのものと異なる場合、フォールバック用に置き換える
        foreach ($timetableMap as $key => $timetable) {
            if ($timetable->subject && $timetable->subject->school_id !== $school_id) {
                $timetable->subject = $fallbackSubject;
            }
        }

        $headerTitle = '担当科目確認';

        return view('users.UserShow', compact(
            'years', 'months', 'grades',
            'selectedYear', 'selectedMonth', 'selectedGrade', 'selectedCourse',
            'weekDays', 'calendar', 'periods', 'timetableMap', 'timetableRecords',
            'firstTimetableId', 'school_id', 'fallbackSubject', 'headerTitle'
        ));
    }

}
