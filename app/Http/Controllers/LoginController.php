<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\Models\Users;
use EasyWeChat\Factory;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'logout'
        ]);
    }


    public function easyWechatGetSession($code)
    {
        $config = config('wechat.mini_program.default');
        $app = Factory::miniProgram($config);
        return $app->auth->session($code);
    }

    /**
     * 处理小程序的自动登陆和注册
     * @param $oauth
     */

    public function login(Request $request)
    {
        // 获取openid
        if ($request->code) {
            $wx_info = $this->easyWechatGetSession($request->code);
        }

        if (empty($wx_info['openid'])) {
            $resultMessage = '用户openid没有获取到';
            $resultCode = 6;
            $err_code = 1;
            $err_msg = '登录失败';
            return json_encode(compact('resultMessage', 'resultCode','err_code','err_msg'));
        }

        $openid = $wx_info['openid'];
        $user = Users::where('openid', $openid)->first();
        if ($user && $user->toArray()) {
            //执行登录
            $user->login_time = Carbon::now();
            $user->name = $request->nickName;

            $user->avatar = $request->avatarUrl;
            $user->save();
            // 直接创建token
            $token =  JWTAuth::fromUser($user);//生成token;
            $err_code = 0;
            $err_msg = '登录成功';
            return json_encode(compact('token', 'user','err_code','err_msg'));
        } else {
            //执行注册
            return $this->register($request, $openid);
        }
    }

    /*
     * 用户注册
    * @param Request $request
    */

    public function register($request, $openid)
    {

        //进行基本验证
        $newUser = [
            'openid' => $openid, //openid
            'name' => $request->nickName, // 昵称git
            'avatar' => $request->avatarUrl, //头像
            'unionid' => '', // unionid (可空)
            'gender' => $request->gender,
            'description' => '太懒了,什么也没写',
            'password' => md5('123456'),
            'login_time' => Carbon::now(),
            'status' => 1,
            'type' => 0,
        ];

        $user = Users::create($newUser);
        $user = Users::where('openid',$user->openid)->first();
        // 直接创建token
        $token = JWTAuth::fromUser($user);//生成token;
        $err_code = 0;
        $err_msg = '登录成功';
        return json_encode(compact('token', 'user','err_code','err_msg'));
    }

    //返回登录状态
    protected function sendFailedLoginResponse(Request $request)
    {
        $resultMessage = $request['errors'];
        $resultCode = $request['code'];
        $err_code = 1;
        $err_msg = '登录失败';
        return  json_encode(compact('resultMessage','resultCode','err_code','err_msg'));
    }

}