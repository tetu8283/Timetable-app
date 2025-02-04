<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'name',
        'email',
        'password',
        'profile_image',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // public function isAdmin()
    // {
    //     return $this->role === 'admin';
    // }

    // public function isTeacher()
    // {
    //     return $this->role === 'teacher';
    // }

    // public function isStudent()
    // {
    //     return $this->role === 'student';
    // }

    // ◆1ユーザ : 多科目
    //   subjectsテーブルの 'school_id' が usersテーブルの 'school_id' を参照
    public function subjects()
    {
        // 第2引数: subjects.school_id (外部キー)
        // 第3引数: users.school_id     (ローカルキー)
        return $this->hasMany(Subject::class, 'school_id', 'school_id');
    }

    // ◆1ユーザ : 多Timetable (中間Subject) を経由する例
    //  -> 自分が持つ subjects に紐づく timetables をまとめて取得したい
    public function timetables()
    {
        // hasManyThrough(最終モデル, 中間モデル, 中間モデルの外部キー, 最終モデルの外部キー, ローカルキー, 中間モデルのローカルキー)
        return $this->hasManyThrough(
            Timetable::class,   // 最終モデル
            Subject::class,     // 中間モデル
            'school_id',        // subjectsテーブルの外部キー(= userを参照しているカラム)
            'subject_id',       // timetablesテーブルの外部キー(= subjectを参照しているカラム)
            'school_id',        // usersテーブルのローカルキー
            'id'                // subjectsテーブルの主キー(デフォルト)
        );
    }
}
