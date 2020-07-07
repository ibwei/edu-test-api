<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('test', function (Blueprint $table) {
            $table->string('scoreArray', 2000)->nullable(false)->default('')->comment('板块得分数组');
            $table->integer('allScore')->nullable(true)->default(0)->comment('总得分');
            $table->integer('status')->nullable(true)->default(0)->comment('0:未查看，1:已查看');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
