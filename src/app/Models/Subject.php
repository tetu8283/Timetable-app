<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['subject_code', 'subject_name', 'school_id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function timetables()
    {
        return $this->belongsToMany(Timetable::class);
    }
}
