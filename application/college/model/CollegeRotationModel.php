<?php

namespace app\college\model;

use think\Model;

class CollegeRotationModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_college_rotation';  //表名
    protected $createTime = 'create_at';  //创建时间
    protected $updateTime = 'update_at';   //修改时间
}
