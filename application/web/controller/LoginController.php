<?php
/**
 * 前台登录
 * Date: 2020/12/18
 */
namespace app\web\controller;

use app\admin\model\LoginLogModel;
use think\facade\View;
use think\Request;
use think\response\Redirect;

class LoginController
{
    /**
     * 登录页面
     * @return string
     */
    public function index()
    {
        return View::fetch('login/index');
    }

    /**
     * 登录验证
     * @param Request $request
     * @return mixed
     */
    public function checkLogin(Request $request)
    {
        $captcha = $request->param('captcha');
        if(!captcha_check($captcha)){
            return json([
                'code' => 101,
                'msg' => '验证码错误'
            ]);
        }
        $come_url = empty(cookie('come_url')) ? '/' : cookie('come_url');
        cookie('come_url',null);
        $username = $request->param('username');
        $password = $request->param('password');
        // 本地验证
//        $userModel = model('WebUser');
//        $data = $userModel->login($username, $password);
//        if (!$data) {
//            return json([
//                'code' => 101,
//                'msg' => $userModel->getError()
//            ]);
//        }

        // 远程验证用户
        $api = '/api/remoteshare/RemoteCheckLogin';
        $param = [
            'username' => $username,
            'password' => $password,
        ];
        $result = adminrequestCurl($api,"POST",$param);
        if(empty($result)){
            return json([
                'code' => 400,
                'msg' => '系统出错'
            ]);
        }
        $loginlog = new LoginLogModel();
        if($result['code'] != 200){
            $userInfo = [
                'id' => 0,
                'username' => $username,
            ];
            // 保存登录日志
            $loginlog->setLoginLog($userInfo,2,$result['error'],1);
            return json([
                'code' => 101,
                'msg' => $result['error']
            ]);
        }
        $userInfo = $result['data'];
        // 保存登录日志
        $loginlog->setLoginLog($userInfo,1,'成功',1);
        //  保存用户信息
        $token = createToken($userInfo['id'],'web');
        session('web_token',$token);
        $data['come_url'] = $come_url;
        return json([
            'code' => 200,
            'data' => $data,
            'msg' => '成功'
        ]);
    }

    /**
     * 退出登录
     * @return Redirect
     */
    public function loginOut()
    {
        session('web_user',null);
        session('web_token',null);
        return Redirect('/login');
    }
}
