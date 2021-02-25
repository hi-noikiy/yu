<?php
/**
 * 后台中间件
 */
namespace app\http\middleware;

use app\admin\model\AdminMenuModel;
use think\Db;
use think\facade\Cache;
use think\facade\Session;

class AdminCheck
{
    public function handle($request, \Closure $next)
    {
        // 判断用户是否登录
        if (!checkToken(Session("admin_token"))) {
            $come_url = array_key_exists('REQUEST_URI',$_SERVER) ? $_SERVER['REQUEST_URI'] : null ;
            cookie('admin_come_url' , $come_url);
            //登录失效处理
            return redirect(url('/admin/login'));
        }
        Session("admin_user",checkToken(Session("admin_token")));
        $redis = Cache::store('redis');
        $admin_user = session('admin_user');
        // 是否为同一设备登录
        if($redis->get('admin_session_id'.$admin_user['id']) != session_id()){
            $come_url = array_key_exists('REQUEST_URI',$_SERVER) ? $_SERVER['REQUEST_URI'] : null ;
            cookie('admin_come_url' , $come_url);
            session('admin_user',null);
            return redirect(url('/admin/login'));
        }
        // 根据不同角色用户添加权限加载菜单
        $admin_menu = new AdminMenuModel();
//        if(! Session::has('admin_menu_'.$admin_user['groupid'])){
        if($admin_user['groupid'] == 1){
            $admin_menu_one = $admin_menu->getMenu();
            if(empty($admin_menu_one)){
                $admin_menu_one = [];
            }
            session('admin_menu_'.$admin_user['groupid'],$admin_menu_one);
        } else {
            $admin_menu_one = $admin_menu->getMenus($admin_user['groupid']);
            if(empty($admin_menu_one)){
                $admin_menu_one = [];
            }
            session('admin_menu_'.$admin_user['groupid'],$admin_menu_one);
        }
//        }
        // 判断是否有访问权限
        if($admin_user['groupid'] != 1){
            $group = Db::name("admin_group")->where('id',$admin_user['groupid'])->value('role');
            $group = explode(',',$group);
            $menu_id = $admin_menu->getMenuId($request->path());
            if(!empty($menu_id)){
                if($menu_id != 10000 && !in_array($menu_id,$group)){
                    // 后期添加前台页面提示
                    return redirect('/admin/msg',['msg'=>'暂无权限,请联系管理员添加']);
                }
            }
        }
        // 获取当前请求菜单的父级ID
        session('parent_id',null);
        $parent_id = $admin_menu->getParentId($request->path());
        session('parent_id',$parent_id);

        return $next($request);
    }
}
