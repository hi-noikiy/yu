<?php
/**
 * 学院模块设置
 * Date: 2020/12/18
 */
namespace app\admin\controller;

use app\college\model\CollegeCategoryModel;
use app\college\model\CollegeRolecategroup;
use app\college\model\CollegeRolecategroupModel;
use app\college\model\CollegeRotationModel;
use app\college\model\CollegeVideoModel;
use think\Db;
use think\facade\View;
use think\Request;

class CollegeController
{
    /**
     * 显示资源列表
     *
     * @return string
     */
    public function index(Request $request)
    {
        $video = $request->param('video');
        $db = Db::name('college_video');
        if(!empty($video)){
            $db->where([
                ['name','like','%'.$video.'%']
            ]);
        }
        $data = $db->alias('a')
            ->field('a.*,b.category')
            ->leftJoin('college_category b','a.category_id = b.id')
            ->order('id','desc')
            ->paginate(10,false,['query' => $request->param()]);
        return View::fetch("college/index",[
            'list' => $data
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return string
     */
    public function create()
    {
        $category_model = new CollegeCategoryModel();
        $category = $category_model->getCategory();
        return View::fetch("college/create",[
            'category' => $category,
        ]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $vadeo_name = $request->param('video_name');
        $video_category = $request->param('video_category');
        $image_url = $request->param('image_url');
        $video_url = $request->param('video_url');
        $video_time = $request->param('video_time');
        $file = $request->param('file');
        $file_name = $request->param('file_name');
        $type = $request->param('type');
        $text_content = $request->param('text_content');
        $admin_user = session('admin_user');
        if($type == 1){
//            // 获取视频时长
//            $getID3 = new \getID3();
//            $videoFile = ltrim($video_url,'/');
//            $videoFileInfo = $getID3->analyze($videoFile);
//            // 获取秒数
//            $audioSecond = $videoFileInfo['playtime_seconds'];
//            //获取格式化时长 00:00
//            $audioFormat = $videoFileInfo['playtime_string'];
            $data['video_time'] = $video_time;
        } elseif($type == 2) {
            $data['course_text'] = $text_content;
        }
        $data['name'] = $vadeo_name;
        $data['url'] = $video_url;
        $data['video_img'] = $image_url;
        $data['total'] = 0;
        $data['fabulous'] = 0;
        $data['owner_id'] = $admin_user['id'];
        $data['category_id'] = $video_category;
        $data['file_url'] = $file;
        $data['file_name'] = $file_name;
        $data['status'] = 1;
        $data['type'] = $type;
        $video_model = new CollegeVideoModel();
        try {
            $video_model->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '添加成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 设置状态
     * @param Request $request
     * @return \think\response\Json
     */
    public function setStatus(Request $request)
    {
        $id = $request->param('id');
        $status = $request->param('status');
        $video_model = new CollegeVideoModel();
        $video = $video_model::get($id);
        if(empty($video)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '视频不存在'
            ]);
        }
        $data['status'] = $status;
        try {
            $video->isUpdate(true)->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '修改成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 200,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }



    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return string
     */
    public function edit($id)
    {
        $video_model = new CollegeVideoModel();
        $video = $video_model::get($id);
        $category_model = new CollegeCategoryModel();
        $category = $category_model->getCategory();
        $video['m3u8_img'] = str_replace('.m3u8','.jpg',$video['url']);
        return View::fetch("college/update",[
            'video' => $video,
            'category' => $category,
        ]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request)
    {
        $id = $request->param('id');
        $vadeo_name = $request->param('video_name');
        $video_category = $request->param('video_category');
        $image_url = $request->param('image_url');
        $video_url = $request->param('video_url');
        $video_time = $request->param('video_time');
        $file = $request->param('file');
        $file_name = $request->param('file_name');
        $type = $request->param('type');
        $text_content = $request->param('text_content');
        $video_model = new CollegeVideoModel();
        $video = $video_model::get($id);
        if(empty($video)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '视频信息不存在'
            ]);
        }
        if($type == 1){
//            // 获取视频时长
//            $getID3 = new \getID3();
//            $videoFile = ltrim($video_url,'/');
//            $videoFileInfo = $getID3->analyze($videoFile);
//            // 获取秒数
//            $audioSecond = $videoFileInfo['playtime_seconds'];
            $data['video_time'] = $video_time;
        } elseif($type == 2) {
            $data['course_text'] = $text_content;
        }
        $data['name'] = $vadeo_name;
        $data['url'] = $video_url;
        $data['video_img'] = $image_url;
        $data['category_id'] = $video_category;
        $data['file_url'] = $file;
        $data['file_name'] = $file_name;
        $data['type'] = $type;
        try {
            $video->isUpdate(true)->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '添加成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }

    /**
     * 视频栏目管理
     * @return string
     * @throws \think\exception\DbException
     */
    public function category(Request $request)
    {
        $search = $request->param('category');
        $category = new CollegeCategoryModel();
        if(!empty($search)){
            $data = $category->getSearch($search);
        } else {
            $data = $category->getCategory();
        }
        return View::fetch('college/category',[
            'list'=>$data,
            'total'=>empty($data) ? 0 : count($data),
        ]);
    }

    /**
     * 添加栏目
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function categoryCreate()
    {
        $category = Db::name('college_category')->where('level',1)->select();
        return View::fetch('college/category_add',['category'=>$category]);
    }

    /**
     * 添加栏目
     * @param Request $request
     * @return \think\response\Json
     */
    public function categorySave(Request $request)
    {
        $category = $request->param('category');
        $pid = $request->param('pid');
        $category_model = New CollegeCategoryModel();
        $check = $category_model->where([
            'category' => $category,
            'pid' => $pid,
        ])->find();
        if(!empty($check)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '栏目已存在'
            ]);
        }
        $data['category'] = $category;
        $data['pid'] = $pid;
        if($pid == 0){
            $data['level'] = 1;
        } else {
            $data['level'] = 2;
        }
        try {
            $category_model->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '添加成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 修改栏目
     * @param $id
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function categoryEdit($id)
    {
        $category = Db::name('college_category')->where('level',1)->select();
        $list = Db::name('college_category')->where('id',$id)->find();
        return View::fetch('college/category_edit',[
            'category'=>$category,
            'list'=>$list,
        ]);
    }

    /**
     * 修改栏目
     * @param Request $request
     * @return \think\response\Json
     */
    public function categoryUpdate(Request $request)
    {
        $category = $request->param('category');
        $pid = $request->param('pid');
        $id = $request->param('id');
        $category_model = New CollegeCategoryModel();
        $check = $category_model::get($id);
        if(empty($check)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '栏目不存在'
            ]);
        }
        $data['category'] = $category;
        $data['pid'] = $pid;
        if($pid == 0){
            $data['level'] = 1;
        } else {
            $data['level'] = 2;
        }
        try {
            $check->isUpdate(true)->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '修改成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 轮播图管理
     * @param Request $request
     * @return string
     * @throws \think\exception\DbException
     */
    public function rotation(Request $request)
    {
        $data = Db::name('college_rotation')->paginate(10);
        return View::fetch("college/rotation",[
            'list' => $data
        ]);
    }

    /**
     * 轮播图添加
     * @return string
     */
    public function rotationCreate()
    {
        return View::fetch("college/rotation_create");
    }

    /**
     * 轮播图添加
     * @param Request $request
     * @return \think\response\Json
     */
    public function rotationSave(Request $request)
    {
        $name = $request->param('name');
        $image = $request->param('image');
        $link = $request->param('link');
        $sort = Db::name("college_rotation")->order('sort','desc')->value('sort');

        $data = [
            'name' => $name,
            'url' => $image,
            'link' => empty($link) ? 'javascript:void(0);' : $link,
            'status' => 1,
            'sort' => empty($sort) ? 1 : $sort+1,
        ];
        try {
            (new CollegeRotationModel())->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '添加成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 修改轮播图状态
     * @param Request $request
     * @return \think\response\Json
     */
    public function rotationStatus(Request $request)
    {
        $id = $request->param('id');
        $status = $request->param('status');
        $rotation = (new CollegeRotationModel())::get($id);
        if(empty($rotation)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '轮播图不存在'
            ]);
        }
        $data = [
            'status' => $status,
        ];
        try {
            $rotation->isUpdate(true)->save($data);
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '修改成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'data' => '',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 轮播图编辑
     * @param $id
     * @return string
     */
    public function rotationEdit($id)
    {
        $data = (new CollegeRotationModel())::get($id);
        return View::fetch("college/rotation_update",[
            'data' => $data
        ]);
    }

    /**
     * 轮播图编辑
     * @param Request $request
     * @return \think\response\Json
     */
    public function rotationUpdate(Request $request)
    {
        $id = $request->param("id");
        $name = $request->param("name");
        $url = $request->param("image");
        $link = $request->param("link");
        $rotation = (new CollegeRotationModel())::get($id);
        if(empty($rotation)){
            return json([
               'code' => 101,
               'msg' => '轮播图不存在',
            ]);
        }
        $data = [
            'name' => $name,
            'url' => $url,
            'link' => empty($link) ? 'javascript:void(0);' : $link,
        ];
        try {
            $rotation->isUpdate(true)->save($data);
            return json([
                'code' => 200,
                'msg' => '修改成功',
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 102,
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
