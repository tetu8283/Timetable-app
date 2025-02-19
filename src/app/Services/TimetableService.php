<?php

namespace App\Services;

use App\Models\Timetable;
use App\Models\Subject;
use Carbon\Carbon;

class TimetableService
{
    /**
     * 指定年月のカレンダー（週ごと、月曜～金曜）を生成
     *
     * @param int $year
     * @param int $month
     * @return array ['calendar' => [...], 'firstDay' => Carbon, 'lastDay' => Carbon]
     */
    public function getCalendar($year, $month)
    {
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay  = $firstDay->copy()->endOfMonth();
        // カレンダーの開始日は月曜に設定
        $startDate = $firstDay->copy()->startOfWeek(Carbon::MONDAY);

        $calendar = [];
        $current  = $startDate->copy();
        while ($current->lte($lastDay)) {
            $week = [];
            for ($i = 0; $i < 5; $i++) {
                $day = $current->copy()->addDays($i);
                // 当月内なら日付、そうでなければ null
                $week[] = ($day->month == $firstDay->month) ? $day : null;
            }
            if (array_filter($week)) {
                $calendar[] = $week;
            }
            $current->addWeek();
        }

        return [
            'calendar' => $calendar,
            'firstDay' => $firstDay,
            'lastDay'  => $lastDay
        ];
    }

    /**
     * 指定年月の四半期カレンダー（週ごと、月曜～金曜）を生成
     *
     * @param int $year
     * @param int $month
     * @return array ['calendar' => [...], 'firstDay' => Carbon, 'lastDay' => Carbon]
     */
    public function getQuarterCalendar($year, $month)
    {
        // 四半期の開始月を算出（例：4,5,6 → 4月スタート）
        $quarterStartMonth = floor(($month - 1) / 3) * 3 + 1;
        $quarterStart = Carbon::create($year, $quarterStartMonth, 1);
        // 四半期の終了日は、開始月＋2月の最終日
        $quarterEnd = Carbon::create($year, $quarterStartMonth + 2, 1)->endOfMonth();

        // カレンダーの開始日は四半期開始日の週の月曜に設定
        $startDate = $quarterStart->copy()->startOfWeek(Carbon::MONDAY);

        $calendar = [];
        $current = $startDate->copy();
        // 四半期終了日を含む週までループ
        while ($current->lte($quarterEnd)) {
            $week = [];
            for ($i = 0; $i < 5; $i++) {
                $day = $current->copy()->addDays($i);
                // 四半期内の日付ならそのまま、四半期外なら null をセット
                $week[] = ($day->between($quarterStart, $quarterEnd, true)) ? $day : null;
            }
            if (array_filter($week)) {
                $calendar[] = $week;
            }
            $current->addWeek();
        }

        return [
            'calendar' => $calendar,
            'firstDay' => $quarterStart,
            'lastDay'  => $quarterEnd
        ];
    }

    /**
     * 指定条件のTimetableレコードを取得し、連想配列に変換
     *
     * @param int        $grade
     * @param int|string $course
     * @param Carbon     $firstDay
     * @param Carbon     $lastDay
     * @return array ['timetableRecords' => Collection, 'timetableMap' => array, 'firstTimetableId' => mixed]
     */
    public function getTimetableMap($grade, $course, $firstDay, $lastDay)
    {
        $timetableRecords = Timetable::with('subject.user')
            ->where('grade', $grade)
            ->where('course_id', $course)
            ->whereBetween('date', [
                $firstDay->format('Y-m-d'),
                $lastDay->format('Y-m-d')
            ])
            ->get();

        $timetableMap = [];
        foreach ($timetableRecords as $t) {
            $key = $t->date . '_' . $t->class_period;
            $timetableMap[$key] = $t;
        }
        $firstTimetableId = $timetableRecords->isEmpty() ? null : $timetableRecords->first()->id;

        return compact('timetableRecords', 'timetableMap', 'firstTimetableId');
    }

    /**
     * バルクインサート処理
     *
     * @param int        $grade
     * @param int|string $course
     * @param array      $subjects subjects[YYYY-MM-DD][コマ] => subject_id
     */
    public function bulkInsertTimetables($year, $month, $grade, $course, array $subjects)
    {
        $insertData = [];

        foreach ($subjects as $date => $periodArray) {

            // ユーザーが選択した年・月に上書きし、日付部分のみをそのまま利用する
            $originalDate = Carbon::createFromFormat('Y-m-d', $date);
            // 選択された年・月に変更
            $correctedDate = Carbon::create($year, $month, $originalDate->day);

            foreach ($periodArray as $period => $subjectId) {
                if (empty($subjectId)) {
                    continue;
                }
                $insertData[] = [
                    'grade'        => $grade,
                    'course_id'    => $course,
                    'class_period' => $period,
                    'date'         => $correctedDate->format('Y-m-d'), // 修正後の正しい日付をセット
                    'subject_id'   => $subjectId,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ];
            }
        }

        if (!empty($insertData)) {
            Timetable::insert($insertData);
        }
    }

    /**
     * 各日付・コマ毎に updateOrCreate を実行して更新
     *
     * @param int        $grade
     * @param int|string $course
     * @param array      $subjects subjects[YYYY-MM-DD][コマ] => subject_id
     */
    public function updateTimetables($grade, $course, array $subjects)
    {
        $emptySubject = Subject::updateOrCreate(
            ['subject_id' => '002'],
            [
                'subject_name' => ' ',
                'school_id'    => 'admin001',
                'color'        => '#ffffff',
                'location'     => ' '
            ]
        );

        $emptySubjectId = $emptySubject->id;

        foreach ($subjects as $date => $periodArray) {

            // 1コマから4コマまでループ
            foreach ($periodArray as $period => $subjectId) {

                if (empty($subjectId)) {
                    $subjectId = $emptySubjectId;
                }

                // 更新または新規作成
                Timetable::updateOrCreate(
                    [
                        // 更新したい同じ学年等を検索する条件
                        'grade'         => $grade,
                        'course_id'     => $course,
                        'date'          => $date,
                        'class_period'  => $period,
                    ],
                    [
                        // 更新または新規作成する値
                        'subject_id'    => $subjectId,
                        'course_id'     => $course,
                        'updated_at'    => now(),
                    ]
                );
            }
        }
    }

    /**
     * 前月の year, month を返す
     */
    public function getPrevMonthYear($year, $month)
    {
        // 月をデクリメント
        $month--;
        // もし1月より小さくなったら、前年の12月に
        if ($month < 1) {
            $month = 12;
            $year--;
        }
        // 配列で返す
        return [$year, $month];
    }

    /**
     * 翌月の year, month を返す
     */
    public function getNextMonthYear($year, $month)
    {
        // 月をインクリメント
        $month++;
        // もし12月より大きくなったら、翌年の1月に
        if ($month > 12) {
            $month = 1;
            $year++;
        }
        return [$year, $month];
    }
}
