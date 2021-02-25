<?php
/**
 * 学院模块
 * @date 2020-12-18
 */
namespace app\college\controller;

use app\college\model\CollegeCategoryModel;
use app\college\model\CollegeOperationModel;
use app\college\model\CollegeVideoModel;
use think\Controller;
use think\Db;
use think\facade\View;
use think\Request;

class IndexController extends Controller
{
    /**
     * 学院首页
     * @return string
     */
    public function index()
    {
        // 轮播图获取
        $rotation = Db::name("college_rotation")->where('status','1')->select();
        return View::fetch('index/index',[
            'rotation' => $rotation
        ]);
    }

    /**
     * 首页获取视频AJAX 返回json
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getVideo(Request $request)
    {
        $type = $request->param('type');
        $categroup = $request->param('categroup');
//        $start_time = strtotime('-5 days');
        $DB = Db::name("college_video");
        switch ($type){
            case 'new':
//                $DB->where('uploader_at','>',$start_time);
                $DB->order('uploader_at','desc');
                break;
            case 'hot':
                $DB->order('total','desc');
                break;
        }
        if(!empty($categroup)){
            $DB->where('category_id',$categroup);
        }
        // 权限判断
        $web_user = session("web_user");
        if($web_user['groupid'] != 1){
            $rolecategroup = Db::name('college_rolecategroup')->where("role",$web_user['groupid'])->find();
            $role = explode(',',$rolecategroup['categroup']);
            $DB->where('category_id','IN',$role);
        }
        $video = $DB->where('status','1')->paginate(10);
        return json([
            'code' => 200,
            'data' => $video,
            'msg' => '请求成功'
        ]);
    }

    /**
     * 查看
     * @return string
     */
    public function read($id)
    {
        $video_model = new CollegeVideoModel();
        $video = $video_model::get($id);
        if(empty($video)){
            return View::fetch('error/no',[
                'type' => 1,
                'msg' => '视频不存在',
            ]);
        }
        // 权限判断
        $web_user = session("web_user");
        if($web_user['groupid'] != 1){
            $rolecategroup = Db::name('college_rolecategroup')->where("role",$web_user['groupid'])->find();
            $role = explode(',',$rolecategroup['categroup']);
            if(!in_array($video['category_id'],$role)){
                return View::fetch('error/no',[
                    'type' => 2,
                    'msg' => '无权限查看该课件',
                ]);
            }
        }
        // 获取栏目
        $category_model = new CollegeCategoryModel();
        $category_nav = $category_model->getCategoryNav($video['category_id']);
        $nav = '';
        if(empty($category_nav)){
            $nav = '详情';
        } else {
            foreach ($category_nav as $v){
                $nav .= $v . ' > ';
            }
            $nav .= '详情';
        }
        // 猜你喜欢
        $like_video = Db::name("college_video")->orderRaw('rand()')->limit(8)->paginate(5);
        // 阅读+1
        Db::name('college_video')->where('id', $id)->setInc('total');
        // 是否点赞
        $user_id = session("web_user")['id'];
        $is_fabulous = Db::name('college_operation')->where([
            'userid' => $user_id,
            'course_id' => $id,
            'type' => 1,
        ])->find();
        return View::fetch('index/play_video',[
            'nav' => $nav,
            'video' => $video,
            'like_video' => $like_video,
            'is_fabulous' => empty($is_fabulous) ? 0 : 1,
            'type' => $video['type'],
        ]);
    }

    /**
     * 点赞
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function setFabulous(Request $request)
    {
        $id = $request->param("id");
        $status = $request->param("status");
        $type = $request->param("type");
        $user_id = session('web_user')['id'];
        // 类型 1 为视频表 2 预留文本表
        if($type == 1){
            $db_name = 'college_video';
        } else {
            $db_name = 'college_video';// 暂无文本表
        }
        if($status == 1){
            // 点赞
            Db::name($db_name)->where('id', $id)->setInc('fabulous');
            if(!empty($user_id)){
                $data = [
                    'userid' => $user_id,
                    'course_id' => $id,
                    'course_type' => $type,
                    'type' => 1,
                    'content' => '',
                ];
                (new CollegeOperationModel())->save($data);
            }
        } else {
            // 取消
            Db::name($db_name)->where('id', $id)->setDec('fabulous');
            if(!empty($user_id)){
                Db::name('college_operation')->where([
                    'userid' => $user_id,
                    'course_id' => $id,
                    'type' => 1,
                ])->delete();
            }
        }
        return json([
            'code' => 200,
            'msg' => '点赞成功'
        ]);
    }

    /**
     * 详细类别页面
     * @param Request $request
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function CateGroup(Request $request)
    {
        $cate_group = $request->param('cate_group');
        $categroup = Db::name("college_category")->where([
            'level'=>1,
        ])->select();
        foreach ($categroup as $key => $val){
            $categroup[$key]['sub'] = Db::name("college_category")->where(['pid'=>$val['id'],])->select();
        }
        return View::fetch('index/categroup',[
            'categroup' => $categroup,
            'cate_group' => $cate_group,
        ]);
    }

    /**
     * 获取文章类别子类
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSubCateGroup(Request $request)
    {
        $id = $request->param('id');
        $categroup = Db::name("college_category")->where('pid',$id)->select();
        if(empty($categroup)){
            return json([
                'code' => 100,
                'msg' => '暂无子类别',
            ]);
        }
        return json([
            'code' => 200,
            'data' => $categroup,
            'msg' => '成功',
        ]);
    }
}
