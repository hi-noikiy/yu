<?php

namespace app\admin\controller;

use app\college\model\CollegeRolecategroupModel;
use app\web\model\WebRoleModel;
use think\Controller;
use think\Db;
use think\facade\View;
use think\Request;

class WebRoleController extends Controller
{
    /**
     * 显示资源列表
     *
     * @return string
     */
    public function index()
    {
        // 远程获取后台角色
        $api = '/api/remoteshare/RemoteGetList';
        $result = adminrequestCurl($api,'GET');
        if($result['code'] != 200){
            $list = [
                'list' => [],
                'dataCount' => 0,
            ];
        } else {
            $list = $result['data'];
            // 更新本地数据
            foreach ($list['list'] as $val){
                $data = (new WebRoleModel())::get($val['id']);
                if(empty($data)){
                    (new WebRoleModel())->save([
                        'id' => $val['id'],
                        'rolename' => $val['rolename'],
                        'status' => 1,
                    ]);
                } else {
                    if($data['rolename'] != $val['rolename']){
                        $data->isUpdate(true)->save([
                            'rolename' => $val['rolename'],
                            'status' => 1,
                        ]);
                    }
                }
            }
        }
//        $list = Db::name("web_role")->where('status',1)->paginate(10);
        return View::fetch('webrole/index',[
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
        // 获取学院视频类别
        $college_category = Db::name('college_category')->where('level',1)->select();
        foreach ($college_category as $key => $val){
            $college_category[$key]['sub'] = Db::name('college_category')->where('pid',$val['id'])->select();
        }
        return View::fetch('webrole/create',[
            'college_category' => $college_category,
        ]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rolename = $request->param('rolename');
        $role_model = new WebRoleModel();
        $role = $role_model->where('rolename',$rolename)->find();
        if(!empty($role)){
            return json([
                'code' => 100,
                'msg' => '角色已存在',
            ]);
        }
        // 处理学院模块权限
        $chk_list = $request->param('chk_list');
        $chk_list_2 = $request->param('chk_list_2');
        $categroup = implode(',',$chk_list).','.implode(',',$chk_list_2);
        try{
            $id = $role_model->insertGetId([
                'rolename' => $rolename,
                'status' => 1,
            ]);
            (new CollegeRolecategroupModel())->save([
                'role' => $id,
                'categroup' => $categroup
            ]);
            return json([
                'code' => 200,
                'msg' => '添加成功',
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 101,
                'msg' => $e->getMessage(),
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
        // 获取学院视频类别
        $college_category = Db::name('college_category')->where('level',1)->select();
        foreach ($college_category as $key => $val){
            $college_category[$key]['sub'] = Db::name('college_category')->where('pid',$val['id'])->select();
        }
        // 获取角色信息
        $web_role_model = new WebRoleModel();
        $web_role = $web_role_model::get($id);
        // 获取已勾选学院权限
        $role = Db::name('college_rolecategroup')->where('role',$id)->value("categroup");
        $role = explode(',',$role);
        return View::fetch('webrole/update',[
            'college_category' => $college_category,
            'web_role' => $web_role,
            'role' => $role,
        ]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $id = $request->param('id');
        $rolename = $request->param('rolename');
        $role_model = new WebRoleModel();
        $role = $role_model->where('id',$id)->find();
        if(empty($role)){
            return json([
                'code' => 100,
                'msg' => '角色不存在',
            ]);
        }
        // 处理学院模块权限
        $chk_list = $request->param('chk_list');
        $chk_list_2 = $request->param('chk_list_2');
        if(!empty($chk_list_2)){
            $categroup = implode(',',$chk_list).','.implode(',',$chk_list_2);
        } else {
            $categroup = implode(',',$chk_list);
        }
        try{
            $role->isUpdate(true)->save([
                'rolename' => $rolename,
                'status' => 1,
            ]);
            $CollegeRolecategroup_model = new CollegeRolecategroupModel();
            $CollegeRolecategroup = $CollegeRolecategroup_model->where("role",$id)->find();
            if(empty($CollegeRolecategroup)){
                $CollegeRolecategroup_model->save([
                    'role' => $id,
                    'categroup' => $categroup
                ]);
            } else {
                $CollegeRolecategroup->isUpdate(true)->save([
                    'categroup' => $categroup
                ]);
            }
            return json([
                'code' => 200,
                'msg' => '添加成功',
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 101,
                'msg' => $e->getMessage(),
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
