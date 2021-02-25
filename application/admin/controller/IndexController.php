<?php
/**
 * 后台首页
 */
namespace app\admin\controller;

use think\facade\View;

class IndexController
{
    public function index()
    {
        return View::fetch('index/index');
    }

    public function msg($msg)
    {
        return View::fetch('index/msg',['msg' => $msg]);
    }
}
