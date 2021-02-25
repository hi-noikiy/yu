<?php

namespace app\smartcloud\model;

use think\Model;

class ScFriendModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_sc_friend';  //表名
    protected $createTime = false;  //创建时间
    protected $updateTime = false;   //修改时间
}
