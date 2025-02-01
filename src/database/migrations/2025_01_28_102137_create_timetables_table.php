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
            $table->unsignedInteger('grade'); // 学年
            $table->unsignedInteger('course_id'); // コースを判別
            $table->unsignedInteger('class_period'); // 何コマ目か
            $table->date('date'); // 日付
            $table->foreignId('subject_id') // 科目ID
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();

            // インデックスの追加 (パフォーマンス向上のため)
            $table->index(['grade', 'course_id', 'date']); // コメント: クエリのパフォーマンス向上のためインデックスを追加
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetables');
    }
};
