<?php
/**
 * 前台用户管理
 * Date: 2020/12/18
 */
namespace app\admin\controller;

use think\Db;
use think\facade\View;
use think\Request;

class WebUserController
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
            $list = Db::name('web_user')->where([
                ['username', 'like', "%".$username."%"]
            ])->order('id desc')->paginate(10,false,['query' => $request->param()]);
        } else {
            $list = Db::name('web_user')->order('id desc')->paginate(10);
        }
        return View::fetch('webuser/index',[
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
        $role = Db::name("web_role")->select();
        return View::fetch('webuser/create',[
            'role' => $role,
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
        $web_user = new \app\web\model\WebUserModel();
        $data['username'] = $request->param('username');
        $check_user = $web_user->where('username',$data['username'])->value('id');
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
        $role = Db::name("web_role")->where('id',$request->param('groupid'))->find();
        $data['groupid'] = $role['id'];
        $data['group'] = $role['rolename'];
        $data['status'] = 1;
        try {
            $result = $web_user->save($data);
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
        $web_user = new \app\web\model\WebUserModel();
        $data['id'] = $request->param('id');
        $check_user = $web_user::get($data['id']);
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
        $web_user = new \app\web\model\WebUserModel();
        $check_user = $web_user::get($id);
        $role = Db::name("web_role")->select();
        return View::fetch('webuser/update',[
            'data'=>$check_user,
            'role' => $role,
        ]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @return string
     */
    public function update(Request $request)
    {
        $id = $request->param('id');
        $web_user = new \app\web\model\WebUserModel();
        $check_user = $web_user::get($id);
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
        $role = Db::name("web_role")->where('id',$request->param('groupid'))->find();
        $data['groupid'] = $role['id'];
        $data['group'] = $role['rolename'];
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
