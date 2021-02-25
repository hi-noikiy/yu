<?php

namespace app\college\model;

use think\Model;

class CollegeOperationModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_college_operation';  //表名
    protected $createTime = 'create_at';  //创建时间
    protected $updateTime = false;   //修改时间
}
