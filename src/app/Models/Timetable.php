<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'course_id',
        'date',
        'class_period',
        'subject_id'
    ];

    // ◆多Timetable : 1Subject
    //   timetablesテーブルの 'subject_id' が subjectsテーブルの 'id' を参照
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
