<?php

namespace app\admin\model;

use think\Model;

class AdminGlobalcodeModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_admin_globalcode';  //表名
    protected $createTime = false;  //创建时间
    protected $updateTime = false;   //修改时间
}
