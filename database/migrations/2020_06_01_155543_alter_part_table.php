<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        //板块表
        Schema::create('part', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('递增ID,主键');
            $table->string('name', 50)->nullable(true)->default('')->comment('版块名');
            $table->string('a_answer', 2000)->nullable(false)->default('')->comment('5-10分评测结果');
            $table->string('b_answer', 2000)->nullable(false)->default('')->comment('11-15分评测结果');
            $table->string('c_answer', 2000)->nullable(false)->default('')->comment('16-20分评测结果');
            $table->string('d_answer', 2000)->nullable(false)->default('')->comment('21-25分评测结果');
            $table->integer('order')->nullable(true)->default(0)->comment('板块排序，值越小，排序越靠前');
            $table->string('description', 50)->nullable(true)->default('')->comment('版块描述');
            $table->softDeletes();
            $table->timestamps();
            $table->unique('name');
        });

        //题目表
        Schema::create('question', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('递增ID,主键');
            $table->string('title',2000)->nullable(false)->default('')->comment('题目');
            $table->integer('part_id')->nullable(false)->comment('板块ID');
            $table->string('a_answer', 300)->nullable(false)->default('')->comment('A答案');
            $table->tinyInteger('a_score')->nullable(false)->default(1)->comment('A得分');
            $table->string('b_answer', 300)->nullable(false)->default('')->comment('B答案');
            $table->tinyInteger('b_score')->nullable(false)->default(1)->comment('B得分');
            $table->string('c_answer', 300)->nullable(false)->default('')->comment('C答案');
            $table->tinyInteger('c_score')->nullable(false)->default(1)->comment('C得分');
            $table->string('d_answer', 300)->nullable(false)->default('')->comment('D答案');
            $table->tinyInteger('d_score')->nullable(false)->default(1)->comment('D得分');
            $table->string('e_answer', 300)->nullable(false)->default('')->comment('E答案');
            $table->tinyInteger('e_score')->nullable(false)->default(1)->comment('E得分');
            $table->tinyInteger('status')->default(1)->comment('0:禁用,1:正常');
            $table->softDeletes();
            $table->timestamps();
        });

        //答题表
        Schema::create('test', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('递增ID,主键');
            $table->integer('user_id')->nullable(false)->comment('答题人ID');
            $table->string('questionArray',2000)->nullable(false)->comment('答题ID数组');
            $table->string('answerArray',2000)->nullable(false)->comment('答案ID数组');
            $table->softDeletes();
            $table->timestamps();
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
        Schema::drop('part');
        Schema::drop('question');
        Schema::drop('test');
    }
}
