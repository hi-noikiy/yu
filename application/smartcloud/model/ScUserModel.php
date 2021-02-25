<?php

namespace app\smartcloud\model;

use think\Model;

class ScUserModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table  = 'yh_sc_user';  //表名
    protected $createTime = 'create_at';  //创建时间
    protected $updateTime = 'update_at';  //修改时间
}
