<?php

namespace app\admin\model;

use think\Model;

class AdminAreaModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_admin_area';  //表名
    protected $createTime = false;  //创建时间
    protected $updateTime = false;   //修改时间
}
