<?php
/**
 * 后台用户管理
 * Date: 2020-12-18
 */
namespace app\admin\controller;

use app\admin\model\AdminUserModel;
use think\Db;
use think\facade\View;
use think\Request;

class UserController
{
    /**
     * 显示资源列表
     *
     * @return string
     */
    public function index(Request $request)
    {
        $username = $request->param('username');
        if(!empty($username)){
            $list = Db::name('admin_user')->where([
                ['username', 'like', "%".$username."%"]
            ])->order('id desc')->paginate(10,false,['query' => $request->param()]);
        } else {
            $list = Db::name('admin_user')->order('id desc')->paginate(10);
        }
        return View::fetch('user/index',[
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
        // 获取角色组
        $group = Db::name('admin_group')->select();
        return View::fetch('user/create',['group'=>$group]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return string
     */
    public function save(Request $request)
    {
        $admin_user = new AdminUserModel();
        $data['username'] = $request->param('username');
        $check_user = $admin_user->where('username',$data['username'])->value('id');
        if(!empty($check_user)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '用户已存在'
            ]);
        }
        $password = $request->param('password');
        $data['salt'] = getsalt();
        $data['truename'] = $request->param('truename');
        $data['password'] = user_md5($password, $data['salt'], $data['username']);
        $data['sex'] = $request->param('sex');
        $data['userphone'] = $request->param('phone');
        $data['groupid'] = $request->param('groupid');
        $group = Db::name('admin_group')->where('id',$data['groupid'])->value('group');
        $data['group'] = $group;
        $data['status'] = 1;
        try {
            $result = $admin_user->save($data);
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
     * 修改用户状态
     * @param Request $request
     * @return \think\response\Json
     */
    public function setStatus(Request $request)
    {
        $admin_user = new AdminUserModel();
        $data['id'] = $request->param('id');
        $check_user = $admin_user::get($data['id']);
        if(empty($check_user)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '用户不存在'
            ]);
        }
        $data['status'] = $request->param('status');
        try {
            $result = $check_user->save(['status'=>$data['status']]);
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
     * 显示指定的资源
     * @param $id
     * @return string
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
        $admin_user = new AdminUserModel();
        $check_user = $admin_user::get($id);
        // 获取角色组
        $group = Db::name('admin_group')->select();
        return View::fetch('user/update',['data'=>$check_user,'group'=>$group]);
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
        $admin_user = new AdminUserModel();
        $check_user = $admin_user::get($id);
        if(empty($check_user)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '用户不存在'
            ]);
        }
        $data['username'] = $request->param('username');
        $password = $request->param('password');
        if(!empty($password)){
            $data['salt'] = getsalt();
            $data['password'] = user_md5($password, $data['salt'], $data['username']);
        }
        $data['truename'] = $request->param('truename');
        $data['sex'] = $request->param('sex');
        $data['userphone'] = $request->param('phone');
        $data['groupid'] = $request->param('groupid');
        $group = Db::name('admin_group')->where('id',$data['groupid'])->value('group');
        $data['group'] = $group;
        try {
            $result = $check_user->isUpdate(true)->save($data);
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
