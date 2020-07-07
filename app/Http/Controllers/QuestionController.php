<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class QuestionController extends Controller
{
    //获取板块列表
    public function list(Request $request)
    {
        $pageSize = $request->pageSize;
        $pageNum = $request->pageNum;
        $title = $request['title'] ? $request['title'] : '';
        $part_id = $request['part_id'] ? $request['part_id'] : [];
        if (count($part_id) == 0) {
            $result = DB::table('question')->join('part', 'question.part_id', '=', 'part.id')->select('question.id', 'question.part_id', 'part.name', 'question.title', 'question.a_answer', 'question.a_score', 'question.b_answer', 'question.b_score', 'question.c_score', 'question.c_answer', 'question.d_answer', 'question.d_score', 'question.e_answer', 'question.e_score', 'question.created_at', 'question.status')->skip($pageSize * ($pageNum - 1))->take(
                $pageSize
            )->whereNull('question.deleted_at')->where([['title', 'like', '%' . $title . '%'], ['status', '=', 1]])->orderBy(
                'question.created_at', 'desc'
            )->get();
        } else {
            $result = DB::table('question')->join('part', 'question.part_id', '=', 'part.id')->select('question.id', 'question.part_id', 'part.name', 'question.title', 'question.a_answer', 'question.a_score', 'question.b_answer', 'question.b_score', 'question.c_score', 'question.c_answer', 'question.d_answer', 'question.d_score', 'question.e_answer', 'question.e_score', 'question.created_at', 'question.status')->skip($pageSize * ($pageNum - 1))->take(
                $pageSize
            )->whereNull('question.deleted_at')->where([['title', 'like', '%' . $title . '%'], ['status', '=', 1]])->whereIn('part_id', $part_id)->orderBy(
                'question.created_at', 'desc'
            )->get();
        }

        $count = Question::all()->count();
        return json_encode(
            [
                'resultCode' => 0,
                'resultMessage' => '获取题库列表成功',
                'data' => $result,
                'total' => $count,
            ]
        );
    }

    public function listByPart(Request $request)
    {
        $pageSize = $request->pageSize;
        $pageNum = $request->pageNum;
        $result = DB::table('question')->join('part', 'question.part_id', '=', 'part.id')->select('question.id', 'question.part_id', 'part.name', 'question.part_id', 'question.title', 'question.a_answer', 'question.a_score', 'question.b_answer', 'question.b_score', 'question.c_score', 'question.c_answer', 'question.d_answer', 'question.d_score', 'question.e_answer', 'question.e_score', 'question.created_at', 'question.status')->skip($pageSize * ($pageNum - 1))->take(
            $pageSize
        )->whereNull('question.deleted_at')->where([['part_id', '=', $request['part_id']], ['status', '=', 1]])->orderBy(
            'question.created_at', 'desc'
        )->get();

        $count = Question::all()->count();
        return json_encode(
            [
                'resultCode' => 0,
                'resultMessage' => '获取题库列表成功',
                'data' => $result,
                'total' => $count,
            ]
        );
    }

    //新增题目
    public function add(Request $request)
    {

        $user = JWTAuth::parseToken()->touser();//获取用户信息

        if (!$user) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '未登录',
                    'data' => []
                ]
            );
        }

        $has = Question::where('title', $request->title)->count();
        if ($has > 0) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '题目名称已经存在，请更换题目',
                    'data' => []
                ]
            );
        }

        $Question = new Question;
        $Question['title'] = $request->title;
        $Question['part_id'] = $request['part_id'];
        $Question['a_answer'] = $request['a_answer'];
        $Question['a_score'] = $request['a_score'];
        $Question['b_answer'] = $request['b_answer'];
        $Question['b_score'] = $request['b_score'];
        $Question['c_answer'] = $request['c_answer'];
        $Question['c_score'] = $request['c_score'];
        $Question['d_answer'] = $request['d_answer'];
        $Question['d_score'] = $request['d_score'];
        $Question['e_answer'] = $request['e_answer'];
        $Question['e_score'] = $request['e_score'];
        $Question['status'] = 1;
        try {
            if ($Question->save()) {
                return json_encode(
                    [
                        'resultCode' => 0,
                        'resultMessage' => '新增题目成功',
                        'data' => []
                    ]
                );
            }

            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '新增题目失败',
                    'data' => []
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '请检查题目是否缺少参数，新增板块失败',
                    'data' => []
                ]
            );
        }
    }

    // 编辑题目详情
    public function update(Request $request)
    {
        try {
            $Question = Question::find($request->id);
            $Question['title'] = $request->title;
            $Question['part_id'] = $request['part_id'];
            $Question['a_answer'] = $request['a_answer'];
            $Question['a_score'] = $request['a_score'];
            $Question['b_answer'] = $request['b_answer'];
            $Question['b_score'] = $request['b_score'];
            $Question['c_answer'] = $request['c_answer'];
            $Question['c_score'] = $request['c_score'];
            $Question['d_answer'] = $request['d_answer'];
            $Question['d_score'] = $request['d_score'];
            $Question['e_answer'] = $request['e_answer'];
            $Question['e_score'] = $request['e_score'];
            $Question['status'] = $request['status'];
            if ($Question->save()) {
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
        } catch (\Exception $e) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '更新失败',
                    'data' => $e
                ]
            );
        }
    }


    public function delete(Request $request)
    {

        $Question = Question::find($request['id']);
        if ($Question->delete()) {
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
