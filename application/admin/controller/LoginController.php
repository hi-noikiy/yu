<?php
/**
 * 后台登录
 * @date 2020-12-18
 */
namespace app\admin\controller;

use app\admin\model\AdminUserModel;
use think\Request;
use think\facade\View;
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
     * @return \think\response\Json
     * @throws \think\exception\PDOException
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
        $come_url = empty(cookie('admin_come_url')) ? '/admin' : cookie('admin_come_url');
        cookie('admin_come_url',null);
        $username = $request->param('username');
        $password = $request->param('password');
        /**
         * @var AdminUserModel $userModel
         */
        $userModel = model('AdminUser');
        $data = $userModel->login($username, $password);
        if (!$data) {
            return json([
                'code' => 101,
                'msg' => $userModel->getError()
            ]);
        }
        $data['admin_come_url'] = $come_url;
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
        session('admin_user',null);
        session('admin_token',null);
        return Redirect('/admin/login');
    }
}
