<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllPainttables  extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //用户表
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('递增ID,主键');
            $table->string('name', 100)->nullable(true)->default('')->comment('用户名');
            $table->string('password', 64)->nullable(true)->default('')->comment('密码');
            $table->string('email', 32)->nullable(true)->default('')->comment('电子邮箱');
            $table->tinyInteger('gender')->unsigned()->nullable(true)->default(1)->comment('性别(1:男,2:女)');
            $table->string('avatar', 255)->nullable(true)->default('')->comment('用户头像');
            $table->string('phone', 50)->nullable(true)->default('')->comment('手机电话');
            $table->string('parent_phone', 50)->nullable(true)->default('')->comment('家长手机电话');
            $table->string('student_name', 50)->nullable(true)->default('')->comment('学生姓名');
            $table->string('grade', 50)->nullable(true)->default('')->comment('就读年级');
            $table->string('school_name', 50)->nullable(true)->default('')->comment('所在学校');
            $table->string('description', 400)->nullable(true)->default('')->comment('用户个人介绍');
            $table->integer('points')->nullable(true)->default(0)->comment('用户个人积分');
            $table->integer('vip')->nullable(true)->default(0)->comment('0:不是,1:是');
            $table->integer('device')->nullable(true)->default(0)->comment('用户注册设备,0:手机,1:电脑');
            $table->string('unionid', 50)->nullable(true)->default('')->comment('微信的unionid');
            $table->string('openid', 255)->nullable(true)->default('')->comment('用户唯一标识openid');
            $table->tinyInteger('type')->nullable(true)->default(0)->comment('0：学生，1：管理员，2：教师');
            $table->string('form_id', 255)->nullable(true)->default('0')->comment('推送模版的id');
            $table->date('login_time')->nullable(true)->comment('记录用户最近一次登录时间');
            $table->tinyInteger('status')->default(1)->comment('0:禁用,1:正常');
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
        Schema::drop('users');
    }
}
