<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
                            'subject_id',
                            'subject_name',
                            'school_id',
                            'location',
                            'color'
                        ];

    public function user()
    {
        return $this->belongsTo(User::class, 'school_id', 'school_id');
    }

    // ◆1Subject : 多Timetable
    //   timetablesテーブルの 'subject_id' が subjectsテーブルの 'id' を参照
    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'subject_id');
    }
}
