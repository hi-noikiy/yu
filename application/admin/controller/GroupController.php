<?php
/**
 * 后台角色管理
 * Date: 2020/12/18
 */
namespace app\admin\controller;

use app\admin\model\AdminMenuModel;
use think\Db;
use think\facade\View;
use think\Request;

class GroupController
{
    /**
     * 显示资源列表
     *
     * @return string
     */
    public function index(Request $request)
    {
        $group = $request->param('group');
        if(!empty($group)){
            $list = Db::name('admin_group')->where([
                ['group', 'like', "%".$group."%"]
            ])->order('id desc')->paginate(10);
        } else {
            $list = Db::name('admin_group')->order('id desc')->paginate(10,false,['query' => $request->param()]);
        }
        return View::fetch('group/index',[
            'list' => $list
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return string
     */
    public function create()
    {
        $admin_menu = new AdminMenuModel();
        $list = $admin_menu->getMenu();
        return View::fetch('group/create',['list'=>$list]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $data['group'] = $request->param('rolename');
        $group = new \app\admin\model\AdminGroupModel();
        if($group->where('group',$data['group'])->value('id')){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '角色已存在'
            ]);
        }
        $chk_list = $request->param('chk_list');
        $chk_list_2 = $request->param('chk_list_2');
        $chk_list_3 = [];
        foreach ($chk_list_2 as $val){
            $subclass = Db::name("admin_menu")->where('parent_id',$val)->select();
            if(!empty($subclass)){
                foreach ($subclass as $vv){
                    $chk_list_3[] = $vv['menu_id'];
                }
            }
        }
//        $chk_list_3 = $request->param('chk_list_3');
        $role = implode(',',$chk_list).','.implode(',',$chk_list_2).','.implode(',',$chk_list_3);
        $data['role'] = $role;
        try {
            $result = $group->save($data);
            if($result){
                return json([
                    'code' => 200,
                    'data' => '',
                    'msg' => '添加成功'
                ]);
            }
        } catch (\Exception $e) {
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return string
     */
    public function edit($id)
    {
        $admin_menu = new AdminMenuModel();
        $list = $admin_menu->getMenu();
        $group = new \app\admin\model\AdminGroupModel();
        $data = $group::get($id);
        $data['role'] = explode(',',$data['role']);
        return View::fetch('group/update',['list'=>$list,'data'=>$data]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $id = $request->param('id');
        $group = new \app\admin\model\AdminGroupModel();
        $check_group = $group::get($id);
        if(empty($check_group)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '角色不存在'
            ]);
        }
        $data['group'] = $request->param('rolename');
        $chk_list = $request->param('chk_list');
        $chk_list_2 = $request->param('chk_list_2');
        $chk_list_3 = [];
        foreach ($chk_list_2 as $val){
            $subclass = Db::name("admin_menu")->where('parent_id',$val)->select();
            if(!empty($subclass)){
                foreach ($subclass as $vv){
                    $chk_list_3[] = $vv['menu_id'];
                }
            }
        }
//        $chk_list_3 = $request->param('chk_list_3');
        $role = implode(',',$chk_list).','.implode(',',$chk_list_2).','.implode(',',$chk_list_3);
        $data['role'] = $role;
        try {
            $result = $check_group->isUpdate(true)->save($data);
            if($result){
                return json([
                    'code' => 200,
                    'data' => '',
                    'msg' => '修改成功'
                ]);
            }
        } catch (\Exception $e) {
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
