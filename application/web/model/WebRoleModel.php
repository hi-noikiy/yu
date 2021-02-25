<?php

namespace app\web\model;

use think\Model;

class WebRoleModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_web_role';  //表名
    protected $createTime = 'create_at';  //创建时间
    protected $updateTime = false;   //修改时间
}
