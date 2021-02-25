<?php
/**
 * 系统设置
 * Date: 2020/12/18
 */
namespace app\admin\controller;

use think\Db;
use think\facade\View;

class SystemController
{
    public function index()
    {
        echo '系统设置';die;
    }

    /**
     * 显示登录日志
     *
     * @return string
     */
    public function loginLog()
    {
        $list = Db::name("login_log")->order('id desc')->paginate(10);
        return View::fetch('system/login_log',['list'=>$list]);
    }
}