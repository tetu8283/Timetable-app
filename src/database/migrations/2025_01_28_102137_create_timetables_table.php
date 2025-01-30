<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            // 学年: 1年/2年
            $table->tinyInteger('grade');
            // コース: 1/2/3 (将来的にマスターテーブルを持つなら course_id でもOK)
            $table->tinyInteger('course');

            // 過去や先の時間割も日別・月別で管理したいので「日付」を明示的に保持
            $table->date('date');

            // 何コマ目か
            $table->tinyInteger('class_period');

            // 科目ID
            $table->foreignId('subject_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();

            // 1日につき同じ学年・コース・コマが複数登録されないようユニークにしておくと便利
            $table->unique(['grade', 'course', 'date', 'class_period'], 'unique_timetable');
        });
    }
};
