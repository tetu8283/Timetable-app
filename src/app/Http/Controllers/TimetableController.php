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

        // プルダウン用の値
        $minYear = $now->year - 2;
        $maxYear = $now->year + 2;
        $years   = range($minYear, $maxYear);
        $months  = range(1, 12);
        $grades  = range(1, 4);
        $weekDays = ['月','火','水','木','金'];
        $periods  = 4; // 1日あたりのコマ数

        // カレンダー作成（カレンダーの初日・最終日も取得）
        $calendarData = $this->timetableService->getCalendar($selectedYear, $selectedMonth);
        $calendar     = $calendarData['calendar'];
        $firstDay     = $calendarData['firstDay'];
        $lastDay      = $calendarData['lastDay'];

        // 今月分のTimetableを一括取得 & 連想配列化
        $timetableData = $this->timetableService->getTimetableMap($selectedGrade, $selectedCourse, $firstDay, $lastDay);
        extract($timetableData); // $timetableRecords, $timetableMap, $firstTimetableId

        return view('timetables.TimetableIndex', compact(
            'years', 'months', 'grades',
            'selectedYear', 'selectedMonth', 'selectedGrade', 'selectedCourse',
            'weekDays', 'calendar', 'periods', 'timetableMap', 'timetableRecords',
            'firstTimetableId'
        ));
    }

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
        $grade  = $request->input('grade');
        $course = $request->input('course');
        // subjects[YYYY-MM-DD][コマ] => 科目ID の配列
        $subjects = $request->input('subjects', []);

        $this->timetableService->bulkInsertTimetables($grade, $course, $subjects);

        return redirect()->route('timetables.index')->with('success', '時間割を登録しました');
    }

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
        extract($timetableData); // $timetableRecords, $timetableMap, 等

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
        $grade  = $request->input('grade');
        $course = $request->input('course');
        // subjects[YYYY-MM-DD][コマ] => 科目ID の配列
        $subjects = $request->input('subjects', []);

        $this->timetableService->updateTimetables($grade, $course, $subjects);

        return redirect()->route('timetables.index')->with('success', '時間割を更新しました');
    }

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
        extract($timetableData); // ここで $timetableRecords, $timetableMap, $firstTimetableId が利用可能

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

        return view('users.UserShow', compact(
            'years', 'months', 'grades',
            'selectedYear', 'selectedMonth', 'selectedGrade', 'selectedCourse',
            'weekDays', 'calendar', 'periods', 'timetableMap', 'timetableRecords',
            'firstTimetableId', 'school_id', 'fallbackSubject'
        ));
    }

}
