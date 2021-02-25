<?php
/**
 * 登录日志
 * @date 2020-12-18
 */
namespace app\admin\model;

use think\facade\Request;
use think\Model;

class LoginLogModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_login_log';  //表名
    protected $createTime = 'login_at';  //创建时间
    protected $updateTime = false;   //修改时间

    /**
     * 登录日志写入
     * @param $data
     * @param $status
     * @param $msg
     */
    public function setLoginLog($data,$status,$msg,$type)
    {
        //写入登录日志
        $this->save([
            'uid' => $data['id'],
            'username' => $data['username'],
            'ip' => Request::instance()->ip(),
            'status' => $status,
            'loginmsg' => $msg,
            'type' => $type,
        ]);
    }
}
