<?php

namespace app\smartcloud\model;

use think\Model;

class ScContactinfoModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_sc_contactinfo';  //表名
    protected $createTime = false;  //创建时间
    protected $updateTime = "update_at";   //修改时间
}
