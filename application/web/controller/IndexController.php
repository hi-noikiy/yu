<?php
/**
 * 前台首页
 */
namespace app\web\controller;

class IndexController
{
    public function index()
    {
        return redirect('/college');
    }
}
