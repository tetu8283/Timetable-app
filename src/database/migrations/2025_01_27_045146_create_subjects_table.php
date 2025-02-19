<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_id')->unique();
            $table->string('subject_name');
            $table->string('school_id');
            $table->string('color')->default('#ffffff');
            $table->string(column: 'location')->nullable();

            $table->foreign('school_id')->references('school_id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        // 外部キー制約を削除
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
        });

        Schema::dropIfExists('subjects');
    }
};
