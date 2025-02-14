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

        // ここで3月を選択しても2月に変わってしまう
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
        // 空の場合の科目レコード（存在しなければ作成）
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
            foreach ($periodArray as $period => $subjectId) {
                if (empty($subjectId)) {
                    $subjectId = $emptySubjectId;
                }

                Timetable::updateOrCreate(
                    [
                        'grade'         => $grade,
                        'course_id'     => $course,
                        'date'          => $date,
                        'class_period'  => $period,
                    ],
                    [
                        'subject_id'    => $subjectId,
                        'course_id'     => $course,
                        'updated_at'    => now(),
                    ]
                );
            }
        }
    }
}
