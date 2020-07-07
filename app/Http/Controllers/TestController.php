<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestController extends Controller
{

    //获取试题列表
    public function list(Request $request)
    {
        try {
            $pageSize = $request->pageSize;
            $pageNum = $request->pageNum;
            $name = $request['student_name'] ? $request['student_name'] : '';
            $data = $request['date'] ? substr($request['date'], 0, 10) : '';
            if ($data) {
                $result = DB::table('test')->join('users', 'test.user_id', '=', 'users.id')->select('test.id', 'users.name', 'users.avatar', 'users.student_name', 'users.parent_phone', 'users.school_name', 'answerArray', 'scoreArray', 'test.allScore', 'test.status', 'test.created_at')->skip($pageSize * ($pageNum - 1))->take(
                    $pageSize
                )->whereNull('test.deleted_at')->whereDate('test.created_at', $data)->where('student_name', 'like', '%' . $name . '%')->orderBy(
                    'test.created_at','desc'
                )->orderBy('test.status', 'asc')->get();
            } else {
                $result = DB::table('test')->join('users', 'test.user_id', '=', 'users.id')->select('test.id', 'users.name', 'users.avatar', 'users.student_name', 'users.parent_phone', 'users.school_name', 'answerArray', 'scoreArray', 'test.allScore', 'test.status', 'test.created_at')->skip($pageSize * ($pageNum - 1))->take(
                    $pageSize
                )->whereNull('test.deleted_at')->where('student_name', 'like', '%' . $name . '%')->orderBy(
                    'test.created_at','desc'
                )->orderBy('test.status', 'asc')->get();
            }

            $count = count($result);
            return json_encode(
                [
                    'resultCode' => 0,
                    'resultMessage' => '获取测试列表成功',
                    'data' => $result,
                    'total' => $count,
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '获取测试列表失败',
                    'data' => json_encode($e),
                ]
            );
        }
    }


    //获取试题列表
    public function wechatHistoryList(Request $request)
    {
        $pageSize = $request->pageSize;
        $pageNum = $request->pageNum;
        $user = JWTAuth::parseToken()->touser();//获取用户信息
        $result = DB::table('test')->join('users', 'test.user_id', '=', 'users.id')->select('test.id', 'users.name', 'users.avatar', 'users.student_name', 'users.parent_phone', 'users.school_name', 'answerArray', 'scoreArray', 'test.allScore', 'test.status', 'test.created_at')->skip($pageSize * ($pageNum - 1))->take(
            $pageSize
        )->whereNull('test.deleted_at')->where('users.id', '=', $user->id)->orderBy(
            'test.created_at', 'desc'
        )->orderBy('test.status', 'asc')->get();

        $count = Test::all()->count();
        return json_encode(
            [
                'err_code' => 0,
                'err_msg' => '获取列表成功',
                'data' => $result,
                'total' => $count,
            ]
        );
    }

    // 根据测试ID获取该测试下有所题目

    public function detail(Request $request)
    {
        try {
            $test = Test::find($request->id);
            $questionArray = explode('-', $test['questionArray']);
            $result = DB::table('question')->join('part', 'question.part_id', '=', 'part.id')->select('question.part_id', 'question.id', 'question.title', 'question.a_answer', 'question.a_score', 'question.b_answer', 'question.b_score', 'question.c_answer', 'question.c_score', 'question.d_answer', 'question.d_score', 'question.e_answer', 'question.e_score', 'question.created_at')->whereIn('question.id', $questionArray)->whereNull('question.deleted_at')->orderBy('part.order', 'asc')->orderBy(
                'part.created_at', 'desc'
            )->get();
            return json_encode(
                [
                    'resultCode' => 0,
                    'resultMessage' => '该次测试数据获取成功',
                    'data' => $result,
                    'answerArray' => $test['answerArray'],
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '获取该次测试数据出错',
                    'data' => $e,
                ]
            );
        }

    }

    //  开始生成题库
    public function generateList()
    {
        try {
            $result = DB::table('part')->select('id')->groupBy('id')->take(10)->whereNull('deleted_at')->orderBy('order', 'asc')->orderBy(
                'created_at', 'desc'
            )->get();
            $index = 0;
            $testArray = [];
            foreach ($result as $key => $value) {
                $temp = Question::where('part_id', $result[$index]->id)->inRandomOrder()->take(5)->get();
                $testArray[$index] = $temp;
                $index++;
            }
            return json_encode(
                [
                    'err_code' => 0,
                    'err_msg' => '获取试题列表成功',
                    'data' => $testArray,
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'err_code' => 1,
                    'err_msg' => '获取试题列表出错',
                    'data' => $e,
                ]
            );
        }


    }


    // 微信根据测试ID获取该测试下有所题目
    public function wechatTestList(Request $request)
    {
        try {
            $test = DB::table('test')->join('users','test.user_id','=','users.id')->select('test.id','allScore','answerArray','test.created_at','questionArray','scoreArray','test.status','test.updated_at','users.student_name','users.grade','users.school_name')->where('test.id', $request->id)->whereNull('test.deleted_at')->get();

            // 微信端不需要展示具体答案,如果需要，取消注释添加$result 回去即可
            // $questionArray = explode('-', $test['questionArray']);
            // $result = DB::table('question')->select('question.id', 'question.title', 'question.a_answer', 'question.a_score', 'question.b_answer', 'question.b_score', 'question.c_answer', 'question.c_score', 'question.d_answer', 'question.d_score', 'question.e_answer', 'question.e_score', 'question.created_at')->whereIn('question.id', $questionArray)->get();


            return json_encode(
                [
                    'err_code' => 0,
                    'err_msg' => '该次测试数据获取成功',
                    'data' => $test
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'err_code' => 1,
                    'err_msg' => '该次测试题目失败,请稍后再试',
                    'data' => $e
                ]
            );
        }
    }


    //提交试题
    public function add(Request $request)
    {

        $user = JWTAuth::parseToken()->touser();//获取用户信息

        if (!$user) {
            return json_encode(
                [
                    'err_code' => 1,
                    'err_msg' => '未登录',
                    'data' => []
                ]
            );
        }
        if (!($user->student_name && $user->grade && $user->school_name && $user->parent_phone)) {
            return json_encode(
                [
                    'err_code' => 1,
                    'err_msg' => '用户信息未完善,请先完善信息',
                    'data' => []
                ]);
        }
        $user = JWTAuth::parseToken()->touser();//获取用户信息
        $Test = new Test;
        $Test['user_id'] = $user->id;
        $Test['questionArray'] = $request['questionArray'];
        $Test['answerArray'] = $request['answerArray'];
        $Test['scoreArray'] = $request['scoreArray'];
        $Test['allScore'] = $request['allScore'];
        $Test['status'] = 0;
        try {
            if ($Test->save()) {

                return json_encode(
                    [
                        'err_code' => 0,
                        'err_msg' => '提交答案成功',
                        'data' => ['id' => $Test->id]
                    ]
                );
            }

            return json_encode(
                [
                    'err_code' => 1,
                    'err_msg' => '提交答案失败',
                    'data' => []
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'err_code' => 1,
                    'err_msg' => '请检查参数',
                    'data' => []
                ]
            );
        }
    }

    // 编辑题目详情
    public function update(Request $request)
    {
        $Test = Test::find($request->id);
        $Test['user_id'] = $request['user_id'];
        $Test['questionArray'] = $request['questionArray'];
        $Test['answerArray'] = $request['answerArray'];
        $Test['scoreArray'] = $request['scoreArray'];
        $Test['allScore'] = $request['allScore'];
        $Test['status'] = $request['status'];
        if ($Test->save()) {
            return json_encode(
                [
                    'resultCode' => 0,
                    'resultMessage' => '更新成功',
                    'data' => []
                ]
            );
        }
        return json_encode(
            [
                'resultCode' => 1,
                'resultMessage' => '更新失败',
                'data' => []
            ]
        );
    }

    // 更改测试状态
    public function handled(Request $request)
    {
        $Test = Test::find($request->id);
        $Test['status'] = $request->status;
        if ($Test->save()) {
            return json_encode(
                [
                    'resultCode' => 0,
                    'resultMessage' => '更新成功',
                    'data' => []
                ]
            );
        }
        return json_encode(
            [
                'resultCode' => 1,
                'resultMessage' => '更新失败',
                'data' => []
            ]
        );
    }


    public function delete(Request $request)
    {

        $Test = Test::find($request['id']);
        if ($Test->delete()) {
            return json_encode(
                [
                    'resultCode' => 0,
                    'resultMessage' => '删除成功',
                    'data' => []
                ]
            );
        }

        return json_encode(
            [
                'resultCode' => 1,
                'resultMessage' => '删除失败',
                'data' => []
            ]
        );


    }
}
