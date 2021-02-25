<?php
/**
 * 智能云模块
 */
namespace app\smartcloud\controller;

use app\smartcloud\model\ScChatassociationModel;
use app\smartcloud\model\ScChatrecordModel;
use app\smartcloud\model\ScContactinfoModel;
use think\Db;
use think\facade\View;
use think\Request;

class IndexController
{
    /**
     * 智能云首页
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 获取该账号下的绑定的微信号
        $web_user = session('web_user');
        $wx_users = Db::name('sc_user')->where(['uid'=>$web_user['id']])->select();
        foreach ($wx_users as $key => $val){
            $wx_users[$key]['nickname'] = rawurldecode($val['nickname']);
            // 暂时使用同步消息判断是否登录
            $wx_api = new WxApiController();
            $result = $wx_api->WXSyncMsg($val['guid'],$val['wxid']);
            $wx_users[$key]['login_status'] = '1';
            if($result['code'] == 202){
                $wx_users[$key]['login_status'] = '-1';
            }
        }
        return View::fetch('index/index',[
            'wx_users' => $wx_users,
            'websocket_ip' => $_SERVER['SERVER_ADDR'] == '127.0.0.1' ? "ws://127.0.0.1:9512" : "ws://8.133.170.162:9512",
        ]);
    }
    public function wxFriendCircle(){
        $web_user = session('web_user');
        $wx_users = Db::name('sc_user')->where(['uid'=>$web_user['id']])->select();
        foreach ($wx_users as $key => $val){
            $wx_users[$key]['nickname'] = rawurldecode($val['nickname']);
            // 暂时使用同步消息判断是否登录
            $wx_api = new WxApiController();
            $result = $wx_api->WXSyncMsg($val['guid'],$val['wxid']);
            $wx_users[$key]['login_status'] = '1';
            if($result['code'] == 202){
                $wx_users[$key]['login_status'] = '-1';
            }
        }
        return View::fetch('index/wxFriendCircle',[
            'wx_users' => $wx_users,
            'websocket_ip' => $_SERVER['SERVER_ADDR'] == '127.0.0.1' ? "ws://127.0.0.1:9512" : "ws://8.133.170.162:9512",
        ]);
    }

    /**
     * 绑定微信页面
     * @return string
     */
    public function bingding()
    {
        return View::fetch("index/binding");
    }

    /**
     * 二次登录
     * @return string
     */
    public function loginTwo(Request $request)
    {
        $wxid = $request->param('wxid');
        $sc_user = Db::name('sc_user')->where([
            'wxid' => $wxid,
            'uid' => session('web_user')['id']
        ])->find();
        return View::fetch("index/two_login",[
            'sc_user' => $sc_user,
        ]);
    }

    /**
     * 获取好友列表
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWxFriendsList(Request $request)
    {
        $wxid = $request->param("wxid");
        // 数据库获取好友列表
        $WXGetContact = Db::name("sc_friend")
            ->alias('a')
            ->join('sc_contactinfo b','b.wxid = a.friendwxid')
            ->where('ownerwxid',$wxid)
            ->field('b.*,a.remark,a.remarkpy')
            ->select();

        if(empty($WXGetContact)){
            return json([
                'code' => 100,
                'msg' => '暂无联系人',
            ]);
        }
        // 排除不需要显示的官方账号
        $filter = [
            "weixin",
            "fmessage",
            "medianote",
            "floatbottle",
            "qmessage",
            "tmessage",
        ];
        foreach ($WXGetContact as $key => $val){
            if(!in_array($val['wxid'],$filter)){
                $val['nickname'] = rawurldecode($val['nickname']);
                if($val['type'] == 1){
                    if(strpos($val['wxid'],'gh_') !== false){
                        $new_WXGetContact[2][] = $val;
                    } else {
                        if(!empty($val['remarkpy'])){
                            if(preg_match ("/^[a-zA-Z]/i", substr( $val['remarkpy'], 0, 1 ) ) ){
                                $new_WXGetContact[0][substr( $val['remarkpy'], 0, 1 )][] = $val;
                            } else {
                                $new_WXGetContact[0]["#"][] = $val;
                            }
                        } else {
                            if(!empty($val['py'])){
                                if(preg_match ("/^[a-zA-Z]/i", substr( $val['py'], 0, 1 ) ) ){
                                    $new_WXGetContact[0][substr( $val['py'], 0, 1 )][] = $val;
                                } else {
                                    $new_WXGetContact[0]["#"][] = $val;
                                }
                            } else {
                                $new_WXGetContact[0]["#"][] = $val;
                            }
                        }
                    }
                } elseif ($val['type'] == 2){
                    $new_WXGetContact[1][] = $val;
                }
            }
        }
        ksort($new_WXGetContact);
        ksort($new_WXGetContact[0]);
        return json([
            'code' => 200,
            'data' => $new_WXGetContact,
            'msg' => '请求成功',
        ]);
    }

    /**
     * 获取聊天列表
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWxChatAssociation(Request $request)
    {
        $wxid = $request->param("wxid");
        // 获取聊天列表
        $WxChatAssociation = Db::name("sc_chatassociation")
                            ->alias('a')
                            ->leftJoin("sc_contactinfo b","a.partyid = b.wxid")
                            ->leftJoin("sc_friend c","c.ownerwxid = a.ourid AND (c.friendwxid = b.wxid)")
                            ->where([
                                'a.ourid' => $wxid,
                                'a.is_delete' => 0,
                                ])
                            ->order('a.time','desc')
                            ->field("a.*,b.nickname,b.headimgurl,b.type,c.remark,c.remarkpy")
                            ->select();

        /*$data = Db::name('sc_chatassociation')
            ->alias('a')
            ->leftJoin('sc_contactinfo b' , "a.partyid = b.wxid")
            ->where([
                'a.ourid'=>$wxid
            ])
            ->select();
        dump($data);die;*/
        if(empty($WxChatAssociation)){
            return json([
                'code' => 100,
                'msg' => '暂无内容',
            ]);
        }
        //dump($WxChatAssociation);
        // 获取最后一条聊天记录,对方信息
        foreach ($WxChatAssociation as $key => $value){
            $WxChatAssociation[$key]['nickname'] = rawurldecode($value['nickname']);
            $WxChatAssociation[$key]['timestr'] = getChatTimeStr($value['time']);
            $WxChatAssociation[$key]['content'] = '';
            $WxChatAssociation[$key]['send_time'] = '';
            $data = Db::name("sc_chatrecord")
                            ->where([
                                'ourid' => $value['ourid'],
                                'partyid' => $value['partyid']
                            ])
                            ->order('send_time','desc')
                            ->find();
            if(!empty($data)){
                $WxChatAssociation[$key]['content'] = rawurldecode($data['content']);
                $WxChatAssociation[$key]['send_time'] = $data['send_time'];
            }
        }
        return json([
            'code' => 200,
            'data' => $WxChatAssociation,
            'msg' => '成功',
        ]);
    }

    /**
     * 获取聊天记录
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWxChatrecord(Request $request)
    {
        $ourid = $request->param("ourid");
        $partyid = $request->param("partyid");
        $page = $request->param("page");
        $offset = $page-1;
        $chatrecord = Db::name("sc_chatrecord")
                    ->where([
                        'ourid' => $ourid,
                        'partyid'=>$partyid,
                        'is_delete'=>0,
                    ])
                    ->order('id','desc')
                    ->limit($offset*10,10)
                    ->select();
        // 区别个人消息  群消息
        $send_type = Db::name("sc_contactinfo")->where("wxid",$partyid)->value("type");
        foreach ($chatrecord as $key => $val){
            $chatrecord[$key]['content'] = rawurldecode($val['content']);
            $chatrecord[$key]['send_time'] = date("Y-m-d H:i:s",$val['send_time']);
            if($send_type == 2){
                $info = Db::name("sc_contactinfo")
                    ->alias('a')
                    ->leftJoin('sc_friend b','a.wxid = b.friendwxid')
                    ->where([
                        "a.wxid" => $val['sp_send'],
                        "b.ownerwxid"=> $partyid,
                    ])
                    ->find();
                $chatrecord[$key]['sp_type'] = 2;
                $chatrecord[$key]['sp_header'] = $info['headimgurl'];
                $chatrecord[$key]['sp_nkname'] = rawurldecode(!empty($info['remark']) ? $info['remark'] : $info['nickname']);
            } else {
                $chatrecord[$key]['sp_type'] = 1;
            }
        }
        //消息关联表修改状态
        $ScChatassociation = ( new ScChatassociationModel())->where(['ourid' => $ourid, 'partyid' => $partyid,])->find();
        if(!empty($ScChatassociation)){
            $ScChatassociation->isUpdate(true)->save(['status'=>0]);
        }
        return json([
            'code' => 200,
            'data' => array_reverse($chatrecord),
            'msg' => '成功'
        ]);
    }

    /**
     * 发送消息
     * @param Request $request
     * @return \think\response\Json
     */
    public function send(Request $request)
    {
        $content = trim($request->param("content"));
        $ourid = $request->param("ourid");
        $partyid = $request->param("partyid");
        // 消息类型 1:文字; 3:图片;34:语音;43:视频;47:动画表情;49:分享;
        $type = $request->param("type");
        // 调用聊天接口
        $guid = Db::name("sc_user")->where('wxid',$ourid)->value('guid');
        //dump($guid);die;
        if(empty($guid)){
            return json([
                'code' => 100,
                'msg' => "微信用户异常"
            ]);
        }
        $wx_api = new WxApiController();
        $params = [
            'partyid' => $partyid,
            'content' => $content,
        ];
        $api_result = $wx_api->WXSendMsg($type,$guid,$params);
        if($api_result['code'] != 200){
            return json([
                'code' => $api_result['code'],
                'msg' => $api_result['msg']
            ]);
        }
//         保存聊天记录
        $newmsgid = $api_result['data'];// 调用接口返回信息
        $data = [
            'newmsgid' => $newmsgid,
            'type' => $type,
            'partyid' => $partyid,
            'ourid' => $ourid,
            'is_send' => 1,
            'send_time' => time(),
        ];
        //dump($data);die;
        switch ($type){
            case 1:
                // 文字
                $data['content'] = emoji_encode($content);;
            break;
            case 3:
                // 图片
                $data['content'] = '[图片]';
                $data['img_base64'] = $content;
            break;
            case 34:
                // 语音(未实现)
                $data['content'] = '[语音]';
            break;
            case 43:
                // 视频(未实现)
                $data['content'] = '[视频]';
            break;
            case 47:
                // 动画表情
                $data['content'] = '[动画表情]';
            break;
            case 49:
                // 分享(未实现)
                $data['content'] = '[分享]';
            break;
        }
        try {
            $ScChatassociationModel = new ScChatassociationModel();
            $chatassociation = $ScChatassociationModel->where(['ourid' => $ourid,'partyid'=>$partyid,'is_delete'=>0])->find();
            if(!empty($chatassociation)){
                $chatassociation->isUpdate(true)->save(['time'=>time()]);
            } else {
                // 保存聊天列表
                $ScChatassociationModel->save([
                    'ourid' => $ourid,
                    'partyid' => $partyid,
                    'time' => time()
                ]);
            }
            (new ScChatrecordModel())->save($data);
            return json([
               'code' => 200,
               'msg' => '发送成功'
            ]);
        } catch (\Exception $e){
            return json([
                'code' => 101,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * 获取联系人详情
     * @param Request $request
     * @return \think\response\Json
     */
    public function getFriendInfo(Request $request){
        $friend_wxid = $request->param('friend_wxid');
        $sccontactinfoModel = new ScContactinfoModel();
        $sccontactinfo = $sccontactinfoModel->where('wxid',$friend_wxid)->find();
        if(empty($sccontactinfo)){
            return json([
               'code' => 201,
               'msg' => '数据异常'
            ]);
        }
        return json([
            'code' => 200,
            'data' => $sccontactinfo,
            'msg' => '成功'
        ]);
    }

    /**
     * 获取群成员
     * @param Request $request
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGroupFriendList(Request $request)
    {
        $wxid = $request->param('wxid');
        $group_friend = Db("sc_friend")
            ->alias('a')
            ->join('sc_contactinfo b','a.friendwxid = b.wxid')
            ->where('ownerwxid',$wxid)
            ->order('a.id','esc')
            ->select();
        foreach ($group_friend as $key => $val){
            $group_friend[$key]['nickname'] = rawurldecode($val['nickname']);
        }
        if(empty($group_friend)){
            return json([
                'code' => 201,
                'msg' => '数据异常'
            ]);
        }
        return json([
            'code' => 200,
            'data' => $group_friend,
            'msg' => '成功'
        ]);
    }

    /**
     * 验证登录状态
     * @param Request $request
     * @return array
     */
    public function checkLoginStatus(Request $request)
    {
        $wxid = $request->param("wxid");
        $web_user = session('web_user');
        $guid = Db::name("sc_user")->where([
            'wxid' => $wxid,
            'uid' => $web_user['id']
        ])->value('guid');
        // 暂时使用同步消息判断是否登录
        $wx_api = new WxApiController();
        $result = $wx_api->WXSyncMsg($guid,$wxid);
        return $result;
    }

    /**
     * 消息撤回
     * @param $newmsgid
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function WXRevokeMsg($newmsgid){
        //dump($newmsgid);die;
        if(empty($newmsgid)){
            return json([
                'code'=> 0,
                'msg'=> '请选择您要撤回的消息'
            ]);
        }
        $data =  Db::name('sc_chatrecord')
            ->alias('a')
            ->join('sc_user b','a.ourid = b.wxid')
            ->where('newmsgid',$newmsgid)
            ->find();
        //dump($data);die;
        if(empty($data)){
            return json([
                'code'=>200,
                'msg'=>'未查询到对应的消息'
            ]);
        }else{
            $wx_api = new WxApiController();
            $result = $wx_api->WXRevokeMsg($data['wxid'],$data['newmsgid'],$data['guid']);

            //dump($result);die;
            if($result['BaseResponse']['Ret'] == -430){
                return json([
                    'code'=>'0',
                    'msg'=>$result['SysWording']
                ]);
            }else{
                return json([
                    'code'=>'200',
                    'msg'=>'该功能正在开发中'
                ]);
            }
        }
    }

    /**
     * 注销微信
     * @param $wxid
     * @return \think\response\Json
     */
    public function WxLonOut($wxid){
        $guid = Db::name('sc_user')->where('wxid',$wxid)->value('guid');
        $wx_api = new WxApiController();
        $result = $wx_api->WxLogOut($guid);
        if($result['code'] == 200 ){
            return json([
               'code'=>200,
               'msg'=>$result['msg']
            ]);
        }else{
            return json([
                'code'=>0,
                'msg'=>$result['msg']
            ]);
        }
    }
    public function Serch($wxid,$v){
        $sql = "SELECT * FROM yh_sc_friend as a JOIN yh_sc_contactinfo as b ON a.friendwxid = b.wxid WHERE a.ownerwxid ='".$wxid."' AND b.nickname like '%".$v."%' OR a.remark like '%.$v.%'";
        $result = Db::query($sql);
        if(empty($result)){
            return json([
                'code'=>'0',
                'msg'=>'未查询到'
            ]);
        }else{
            return json([
                'code'=>'200',
                'msg'=>'查询成功',
                'data'=>$result
            ]);
        }
    }
}
