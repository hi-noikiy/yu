<?php
/**
 * 菜单表
 * @date 2020/12/18
 */
namespace app\admin\model;

use think\Db;
use think\Model;

class AdminMenuModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_admin_menu';  //表名
    protected $createTime = 'create_at';  //创建时间
    protected $updateTime = 'update_at';   //修改时间

    /**
     * 获取全部菜单
     * @return array|\PDOStatement|string|\think\Collection|\think\model\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMenu()
    {
        $admin_menu_one = Db::name("admin_menu")->where('level',1)->select();
        foreach ($admin_menu_one as $key => $val){
            $admin_menu_one[$key]['subclass'] = [];
            $admin_menu_two = Db::name("admin_menu")->where('parent_id',$val['menu_id'])->select();
            $admin_menu_one[$key]['subclass'] = $admin_menu_two;
            if(!empty($admin_menu_two)){
                foreach ($admin_menu_two as $kk => $vv){
                    $admin_menu_one[$key]['subclass'][$kk]['subclass'] = [];
                    $admin_menu_tree = Db::name("admin_menu")->where('parent_id',$vv['menu_id'])->select();
                    $admin_menu_one[$key]['subclass'][$kk]['subclass'] = $admin_menu_tree;
                }
            }
        }
        return $admin_menu_one;
    }

    /**
     * 根据角色获取菜单
     * @param $group_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMenus($group_id)
    {
        $group = Db::name('admin_group')->where('id',$group_id)->value('role');
        $group = explode(',',$group);
        foreach ($group as $value){
            $data = Db::name("admin_menu")->where(['menu_id'=>$value,'level'=>1])->find();
            $data_2 = Db::name("admin_menu")->where(['menu_id'=>$value,'level'=>2])->find();
            $data_3 = Db::name("admin_menu")->where(['menu_id'=>$value,'level'=>3])->find();
            if(!empty($data)){
                $admin_menu_one[] = $data;
            }
            if(!empty($data_2)){
                $admin_menu_two[] = $data_2;
            }
            if(!empty($data_3)){
                $admin_menu_tree[] = $data_3;
            }
        }
        foreach ($admin_menu_one as $key => $val){
            $admin_menu_one[$key]['subclass'] = [];
            if(!empty($admin_menu_two)){
                foreach ($admin_menu_two as $kk => $vv){
                    $admin_menu_two[$kk]['subclass'] = [];
                    if(!empty($admin_menu_tree)){
                        foreach ($admin_menu_tree as $kkk => $vvv){
                            if($vv['menu_id'] == $vvv['parent_id']){
                                $admin_menu_two[$kk]['subclass'][] = $vvv;
                            }
                        }
                    }
                    if($val['menu_id'] == $vv['parent_id']){
                        $admin_menu_one[$key]['subclass'][] = $admin_menu_two[$kk];
                    }
                }
            }
        }
        return $admin_menu_one;
    }

    /**
     * 根据路由获取当前菜单的父级ID
     * @param $route
     * @return mixed|string
     */
    public function getParentId($route)
    {
        $routes = '/'.$route;
        $admin_menu = Db::name("admin_menu")->where('route',$routes)->value('parent_id');
        if(empty($admin_menu)){
            return '';
        }
        return $admin_menu;
    }

    public function getMenuId($route)
    {
        $routes = '/'.$route;
        $admin_menu = Db::name("admin_menu")->where(['route'=>$routes,'level'=>2])->value('menu_id');
        if(empty($admin_menu)){
            return '';
        }
        return $admin_menu;
    }
}
