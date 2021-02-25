<?php
/**
 * 学院视频栏目表
 */
namespace app\college\model;

use think\Db;
use think\Model;

class CollegeCategoryModel extends Model
{
    protected $pk = 'id';  //主键
    protected $table = 'yh_college_category';  //表名
    protected $createTime = false;  //创建时间
    protected $updateTime = false;   //修改时间

    /**
     * 获取栏目
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCategory()
    {
        $level_1 = Db::name('college_category')->where([
            'level' => 1
        ])->select();
        $data = [];
        foreach ($level_1 as $value){
            $value['pname'] = '';
            $data[] = $value;
            $level_2 = Db::name('college_category')->where([
                'level' => 2,
                'pid' => $value['id']
            ])->select();
            if(!empty($level_2)){
                foreach ($level_2 as $vv){
                    $vv['category'] = '|---------'.$vv['category'];
                    $vv['pname'] = $value['category'];
                    $data[] = $vv;
                }
            }
        }
        return $data;
    }

    /**
     * 根据搜索获取栏目
     * @param $search
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSearch($search)
    {
        $category = Db::name('college_category')->where([
            ['category' ,'like', '%'.$search.'%'],
        ])->select();
        $data = [];
        foreach ($category as $value){
            if($value['level'] == 2){
                $level_1 = Db::name('college_category')->where([
                    'id' => $value['pid']
                ])->find();
                $value['pname'] = $level_1['category'];
            }else{
                $value['pname'] = '';
            }
            $data[] = $value;
            if($value['level'] == 1){
                $level_2 = Db::name('college_category')->where([
                    'level' => 2,
                    'pid' => $value['id']
                ])->select();
                if(!empty($level_2)){
                    foreach ($level_2 as $vv){
                        $vv['category'] = '|---------'.$vv['category'];
                        $vv['pname'] = $value['category'];
                        $data[] = $vv;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 根据Id获取栏目
     * @param $id
     * @return array
     */
    public function getCategoryNav($id)
    {
        $data = $this::get($id);
        $category_nav = [];
        if(!empty($data)){
            if($data['level'] == 2){
                $p_category = $this::get($data['pid']);
                if(!empty($p_category)){
                    $category_nav[] = $p_category['category'];
                }
            }
            $category_nav[] = $data['category'];
        }
        return $category_nav;
    }
}
