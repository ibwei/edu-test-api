<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */


// 微信端

// 不需要 token

Route::get('/test/hello',function(){ return 'hello';});
// 登录| 自动注册
Route::post('/wechat/user/login', 'LoginController@login')->name('login');


Route::get('/test/part/list', 'PartController@wechatTestList');
// 获取排序好了的板块列表
Route::get('/wechat/part/list', 'PartController@wechatList');
//

// 获取测试结果
Route::post('/wechat/test/result','TestController@wechatTestList');

Route::group(['middleware' => 'api.auth','prefix'=>'wechat'], function () {

    //需要 token

    // 获取测试题目
    Route::get('/test/list','TestController@generateList');
    // 获取排序好了的板块列表
    // 更新 用户家长电话,学生姓名,年纪,学校
    Route::post('user/completedInfo','AuthController@completedInfo');
    //提交试卷
    Route::post('/test/add', 'TestController@add');
    // 获取历史提交记录的摘要
    Route::post('/test/history', 'TestController@wechatHistoryList');


});


// 后台管理系统

Route::post('/user/login', 'AuthController@login');//登录
Route::post('/user/register', 'AuthController@register');//注册

Route::group(
    ['middleware' => 'api.auth'], function () {

    Route::post('/user/getUserInfo', 'AuthController@getUserInfo');//获取用户信息
    Route::post('/user/logout', 'AuthController@logout');//退出
    Route::post('/user/list', 'AuthController@list');
    Route::post('/user/update', 'AuthController@update');
    Route::post('/user/updatePassword', 'AuthController@updatePassword');
    Route::post('/user/delete', 'AuthController@delete');
    Route::post('/dashboard', 'DashboardController@index');//主板面信息

    Route::post('/part/add', 'PartController@add');//添加板块
    Route::post('/part/update', 'PartController@update');//更新板块
    Route::post('/part/delete', 'PartController@delete');//删除板块

    Route::post('/question/add', 'QuestionController@add');//添加题目
    Route::post('/question/update', 'QuestionController@update');//更新题目
    Route::post('/question/delete', 'QuestionController@delete');//删除题目

    Route::post('/test/handled', 'TestController@handled');//已经阅卷
    Route::post('/test/delete', 'TestController@delete');//删除试卷
    Route::post('/test/list', 'TestController@list');//已提交试卷列表
    Route::post('/test/detail', 'TestController@detail');//已提交试卷列表
},


    //-----------------------------不需要token--------------------------

    Route::post('/part/list', 'PartController@list'),//板块列表
    //新增
    Route::post('/question/list', 'QuestionController@list'),
    Route::post('/question/listbypart', 'QuestionController@listByPart'),
    //更新题目
    Route::post('/test/update', 'TestController@update')

);
