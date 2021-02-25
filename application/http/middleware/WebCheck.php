<?php

namespace app\http\middleware;

use app\admin\model\LoginLogModel;
use think\facade\Request;
use think\facade\Session;

class WebCheck
{
    public function handle($request, \Closure $next)
    {
        // 判断用户来源是否为OA
        if(isset($_SERVER['HTTP_REFERER'])){
            if(strpos($_SERVER['HTTP_REFERER'],'yuhsw.com')){ // 线上
//            if(strpos($_SERVER['HTTP_REFERER'],'localhost') || strpos($_SERVER['HTTP_REFERER'],'127.0.0.1')){ // 本地
                $token = $request->param('token');
                if(!empty($token)){
                    $result = adminrequestCurl('/api/remoteshare/RemoteGetToken',"POST",['token'=>$token]);
                    if($result['code'] == 200){
                        $userInfo = $result['data'];
                        if($userInfo['id'] != checkToken(Session("web_token"))['id']){
                            Session("web_user",null);
                            //  保存用户信息
                            $token = createToken($userInfo['id'],'web');
                            session('web_token',$token);
                            // 记录登录信息
                            $loginlog = new LoginLogModel();
                            $loginlog->setLoginLog($userInfo,1,'OA系统登录',1);
                        }
                        $path = Request::instance()->pathinfo();
                        if(!empty($path)){
                            return redirect(url('/'.$path));
                        }
                        return redirect(url('/'));
                    }
                }
            }
        }
        // 判断用户是否登录
        if (!checkToken(Session("web_token"))) {
            $come_url = array_key_exists('REQUEST_URI',$_SERVER) ? $_SERVER['REQUEST_URI'] : null ;
            cookie('come_url' , $come_url);
            //登录失效处理
            return redirect(url('/login'));
        }
        if(empty(Session("web_user"))){
            Session("web_user",checkToken(Session("web_token")));
        }
        return $next($request);
    }
}
