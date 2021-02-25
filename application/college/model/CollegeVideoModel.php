<?php
/**
 * 学院模块视频
 */
namespace app\college\model;

use think\Model;

class CollegeVideoModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_college_video';  //表名
    protected $createTime = 'uploader_at';  //创建时间
    protected $updateTime = false;   //修改时间
}
