<?php
/**
 * 角色表
 * @date 2020/12/18
 */
namespace app\admin\model;

use think\Model;

class AdminGroupModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_admin_group';  //表名
    protected $createTime = 'create_at';  //创建时间
    protected $updateTime = 'update_at';   //修改时间
}
