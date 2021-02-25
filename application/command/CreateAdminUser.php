<?php
/**
 * 生成超级管理员
 * @date 2020-12-18
 */
namespace app\command;

use app\admin\model\AdminUserModel;
use app\admin\model\AdminGroupModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class CreateAdminUser extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('createuser')
            ->setDescription('Create Admin User');;
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln("Create Admin User Start");
        $output->writeln("========================");
        $admin_user = new AdminUserModel();
        $data['username'] = 'admin';
        $check_user = $admin_user->where('username',$data['username'])->value('id');
        if(!empty($check_user)){
            $output->writeln("User Exists");
            $output->writeln("========================");
        }
        $password = 'admin@123';
        $data['salt'] = getsalt();
        $data['truename'] = "系统自动生成";
        $data['password'] = user_md5($password, $data['salt'], $data['username']);
        $data['sex'] = 1;
        $data['userphone'] = '13000000000';
        $data['groupid'] = 1;
        $data['group'] = '超级管理员';
        $data['status'] = 1;
        $group = new AdminGroupModel();
        $group_data['group'] = '超级管理员';
        $group_data['role'] = '';

        try {
            $result_group = $group->save($group_data);
            $result = $admin_user->save($data);
            if($result && $result_group){
                $output->writeln("username : admin , password : admin@123");
                $output->writeln("========================");
                $output->writeln("Create OK");
            }
        } catch (\Exception $e) {
            $output->writeln("error : ".$e->getMessage());
        }
    }
}
