<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class PartController extends Controller
{

    //获取板块列表
    public function list(Request $request)
    {
        $pageSize = $request->pageSize;
        $pageNum = $request->pageNum;
        $name = $request['name'] ? $request['name'] : '';
        $result = DB::table('part')->select('id', 'name', 'a_answer', 'b_answer', 'c_answer', 'd_answer', 'order', 'created_at')->skip($pageSize * ($pageNum - 1))->take(
            $pageSize
        )->whereNull('deleted_at')->where('name', 'like', '%' . $name . '%')->orderBy(
            'created_at', 'desc'
        )->get();

        $count = Part::all()->count();
        return json_encode(
            [
                'resultCode' => 0,
                'resultMessage' => '获取板块列表成功',
                'data' => $result,
                'total' => $count,
            ]
        );
    }


    public function wechatTestList()
    {

        $result = DB::table('part')->select('id', 'name', 'a_answer', 'b_answer', 'c_answer', 'd_answer', 'order', 'created_at')->whereNull('deleted_at')->orderBy(
            'created_at', 'desc'
        )->get();
        return json_encode(
            [
                'resultCode' => 0,
                'resultMessage' => '获取板块列表成功',
                'data' => $result,
            ]
        );
    }

    // 微信获取板块列表

    public function wechatList()
    {
        try {
            $result = DB::table('part')->select('id', 'name', 'a_answer', 'b_answer', 'c_answer', 'd_answer', 'order', 'created_at')->whereNull('deleted_at')->orderBy(
                'created_at', 'desc'
            )->get();
            return json_encode(
                [
                    'err_code' => 0,
                    'err_msg' => '获取板块列表成功',
                    'data' => $result,
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '获取板块列表失败',
                    'data' => []
                ]
            );
        }
    }

    //新增板块
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
        $count = Part::all()->count();
        if ($count >= 10) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '板块数量已经达到10个，请先删除模块再添加！',
                    'data' => []
                ]
            );
        }
        $part = new Part;
        $part['order'] = 0;
        $part['name'] = $request->name;
        $part['a_answer'] = $request['a_answer'];
        $part['b_answer'] = $request['b_answer'];
        $part['c_answer'] = $request['c_answer'];
        $part['d_answer'] = $request['d_answer'];
        try {
            if ($part->save()) {
                return json_encode(
                    [
                        'resultCode' => 0,
                        'resultMessage' => '新增板块成功',
                        'data' => []
                    ]
                );
            }

            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '新增板块失败',
                    'data' => []
                ]
            );
        } catch (\Exception $e) {
            return json_encode(
                [
                    'resultCode' => 1,
                    'resultMessage' => '请检查版块名是否重复，新增板块失败',
                    'data' => []
                ]
            );
        }
    }

    // 编辑板块详情
    public function update(Request $request)
    {
        $part = Part::find($request->id);
        $part['order'] = 0;
        $part['name'] = $request->name;
        $part['a_answer'] = $request['a_answer'];
        $part['b_answer'] = $request['b_answer'];
        $part['c_answer'] = $request['c_answer'];
        $part['d_answer'] = $request['d_answer'];
        if ($part->save()) {
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
                'resultCode' => 0,
                'resultMessage' => '更新失败',
                'data' => []
            ]
        );
    }


    public function delete(Request $request)
    {

        $part = Part::find($request['id']);
        if ($part->delete()) {
            $result = DB::table('part')->select('id', 'name', 'a_answer', 'b_answer', 'c_answer', 'd_answer', 'order', 'created_at')->take(10)->whereNull('deleted_at')->orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
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
