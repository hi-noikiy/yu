<?php
/**
 * 微信API接口
 * Date: 2020/12/18
 */
namespace app\smartcloud\controller;

use app\smartcloud\model\ScChatassociationModel;
use app\smartcloud\model\ScChatrecordModel;
use app\smartcloud\model\ScContactinfoModel;
use app\smartcloud\model\ScFriendModel;
use app\smartcloud\model\ScUserModel;
use think\Db;
use think\facade\View;
use think\Request;
use think\response\Json;

class WxApiController
{
    /**
     * 创建微信,获取guid
     * 返回 数组
     * @return mixed|string
     */
    public function WXCreate()
    {
        $api = "/api/Client/WXCreate";
        $params = [
            "Terminal"=> 2,
            "WxData"=> "",
            "Brand"=> "",
            "Name"=> "",
            "Imei"=> "",
            "Mac"=> ""
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200){
            return '';
        }
        return $result['data']['data']['Guid'];
    }

    /**
     * 获取微信登录二维码
     * 返回 json
     * @return string|\think\response\Json
     */
    public function WXLoginQrcode()
    {
        // 创建微信,获取guid
        $guid = $this->WXCreate();
        if(empty($guid)){
            return json([
                'code' => 400,
                'data' => '',
                'msg' => 'guid请求失败'
            ]);
        }
        $api = "/api/Login/WXGetLoginQrcode";
        $params = [
            "Guid"=> $guid,
        ];
        // 请求登录二维码
        $result = $this->requestCurl($api,$params);
        //dump($result);die;
        if($result['code'] != 200){
            return json([
                'code' => 400,
                'data' => '',
                'msg' => '二维码请求失败'
            ]);
        }
        $uuid = $result['data']['data']['uuid'];
        $qrcode = $result['data']['data']['qrcode'];
        $data = [
            'guid' => $guid,
            'uuid' => $uuid,
            'qrcode_base64' => 'data:image/jpg;base64,'.$qrcode
        ];
        return json([
            'code' => 200,
            'data' => $data,
            'msg' => '请求成功'
        ]);
    }

    /**
     * 获取扫码结果
     * 返回 json
     * @param Request $request
     * @return \think\response\Json
     */
    public function WXCheckLoginQrcode(Request $request)
    {
        $uuid = $request->param('uuid');
        $guid = $request->param('guid');
        // 登录类型 1 首次绑定账号,2二次登陆
        $type  = $request->param('type');
        $api = "/api/Login/WXCheckLoginQrcode";
        $params = [
            "Uuid" => $uuid,
            "Guid" => $guid,
        ];
        // 获取扫码结果
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200){
            return json([
                'code' => 400,
                'data' => '',
                'msg' => '扫码结果请求失败'
            ]);
        }
        switch ($result['data']['data']['state']){
            case '0':
                return json([
                    'code' => 201,
                    'data' => '',
                    'msg' => '请扫描登录二维码'
                ]);
                break;
            case '1':
                return json([
                    'code' => 202,
                    'data' => [
                        'headImgUrl' => $result['data']['data']['headImgUrl'],
                        'nickName' => $result['data']['data']['nickName'],
                    ],
                    'msg' => '请点击登录按钮'
                ]);
                break;
            case '2':
                $data = [
                    'guid' => $guid,
                    'uuid' => $uuid,
                    'headImgUrl' => $result['data']['data']['headImgUrl'],
                    'nickName' => $result['data']['data']['nickName'],
                    'wxid' => $result['data']['data']['wxid'],
                    'wxnewpass' => $result['data']['data']['wxnewpass'],
                ];
                return json([
                        'code' => 200,
                        'data' => $data,
                        'msg' => '请求成功'
                    ]);
                break;
            default:
                return json([
                    'code' => 400,
                    'data' => '',
                    'msg' => '扫码状态请求失败'
                ]);
        }
    }

    /**
     * 绑定,保存用户信息
     * 返回数组
     * @param $data
     * @param $type // 登录类型 1 首次绑定账号,2二次登陆
     * @return Json
     */
    public function WXLoginManual(Request $request)
    {
        $data = $request->param("data");
        $type = $request->param("type");
        $api = "/api/Login/WXLoginManual"; //人工登录
        $params = [
            "Channel"=> 1,
            "UserName"=> $data['wxid'],
            "Password"=> $data['wxnewpass'],
            "Guid"=> $data['guid'],
        ];
        //保存微信用户信息
        $web_user = session('web_user');
        $wx_user = new ScUserModel();
        try{
            // 判断首次登录,二次登陆
            if($type == 1){
                $check_wx = $wx_user->where('wxid',$data['wxid'])->find();
                if(!empty($check_wx)){
                    // 有该账号信息,判断状态,异常状态可以正常登录
                    if($check_wx['status'] == 1){
                        return json([
                            'code' => 101,
                            'data' => '',
                            'msg' => '此微信已被绑定'
                        ]);
                    } else {
                        //登录
                        $result = $this->requestCurl($api,$params);
                        if($result['code'] != 200){
                            return json([
                                'code' => 400,
                                'data' => '',
                                'msg' => '登录失败'
                            ]);
                        }
                        $user_data = [
                            'wxid' => $data['wxid'],
                            'wxpassword' => $data['wxnewpass'],
                            'nickname' => $this->emoji_encode($data['nickName']),
                            'headimgurl' => $data['headImgUrl'],
                            'alias' => $result['data']['data']['accountInfo']["Alias"],
                            'uuid' => $data['uuid'],
                            'guid' => $data['guid'],
                            'status' => 1,
                            'login_at' => time(),
                            'uid' => $web_user['id'],
                            'uname' => $web_user['username'],
                        ];
                        $check_wx->isUpdate(true)->save($user_data);
                    }
                } else {
                    //登录
                    $result = $this->requestCurl($api,$params);
                    if($result['code'] != 200){
                        return json([
                            'code' => 400,
                            'data' => '',
                            'msg' => '登录失败'
                        ]);
                    }
                    $wx_user->save([
                        'wxid' => $data['wxid'],
                        'wxpassword' => $data['wxnewpass'],
                        'nickname' => $this->emoji_encode($data['nickName']),
                        'headimgurl' => $data['headImgUrl'],
                        'alias' => $result['data']['data']['accountInfo']["Alias"],
                        'uuid' => $data['uuid'],
                        'guid' => $data['guid'],
                        'status' => 1,
                        'login_at' => time(),
                        'uid' => $web_user['id'],
                        'uname' => $web_user['username'],
                    ]);
                    // 第一次绑定获取账号详细信息
                    $WXGetProfile_result = $this->WXGetProfile($data['guid'],$data['wxid']);
                    if($WXGetProfile_result['code'] != 200){
                        return json([
                            'code' => 400,
                            'data' => '',
                            'msg' => '详细信息获取失败:'.$WXGetProfile_result['msg']
                        ]);
                    }
                }
            } else {
                // 二次登录
                $check_wx = $wx_user->where(['wxid'=>$data['wxid'],'uid'=>$web_user['id']])->find();
                if(empty($check_wx)){
                    return json([
                        'code' => 101,
                        'data' => '',
                        'msg' => '此微信未被绑定'
                    ]);
                }
                //登录
                $result = $this->requestCurl($api,$params);
                if($result['code'] != 200){
                    return json([
                        'code' => 400,
                        'data' => '',
                        'msg' => '登录失败'
                    ]);
                }
                $check_wx->isUpdate(true)->save([
                    'wxpassword' => $data['wxnewpass'],
                    'nickname' => $this->emoji_encode($data['nickName']),
                    'headimgurl' => $data['headImgUrl'],
                    'uuid' => $data['uuid'],
                    'status' => '1',
                    'login_at' => time(),
                ]);
            }
            return json([
                'code' => 200,
                'data' => '',
                'msg' => '请求成功'
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 400,
                'data' => '',
                'msg' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 扫码二次登陆UUID
     * @param Request $request
     * @return Json
     */
    public function WXPushLoginQrcode(Request $request)
    {
        $api = "/api/Login/WXPushLoginQrcode";
        $wxid = $request->param('wxid');
        $guid = $request->param('guid');
        $web_user = session('web_user');
        $uid = $web_user['id'];
        $wx_user = new ScUserModel();
        $check_wx = $wx_user->where(['wxid'=>$wxid,'uid'=>$uid])->find();
        if(empty($check_wx)){
            return json([
                'code' => 101,
                'data' => '',
                'msg' => '此微信未被绑定'
            ]);
        }
        $params = [
            "Guid"=> $guid,
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200){
            return json([
                'code' => 400,
                'data' => '',
                'msg' => '二次登录失败'
            ]);
        }
        $uuid = $result['data']['data']['uuid'];
        if(empty($uuid)){
            // guid失效,修改账号状态,允许重新绑定账号
            $check_wx->isUpdate(true)->save(['status'=>2]);
            return json([
                'code' => 400,
                'data' => '',
                'msg' => 'uuid获取失败,可重新绑定账号'
            ]);
        }
        return json([
            'code' => 200,
            'data' => $uuid,
            'msg' => '请求成功'
        ]);
    }

    /**
     * 获取账户的详细信息
     * 返回数组
     * @param $guid
     * @param $wxid
     * @return array|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function WXGetProfile($guid,$wxid)
    {
        $api = '/api/User/WXGetProfile';
        $params = [
            "Guid"=> $guid,
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200){
            return [
                'code' => 400,
                'data' => '',
                'msg' => '获取用户信息失败'
            ];
        }
        $wx_user = new ScUserModel();
        $check_wx = $wx_user->where('wxid',$wxid)->find();
        if(empty($check_wx)){
            return [
                'code' => 101,
                'data' => '',
                'msg' => '此微信未绑定'
            ];
        }
        $data = $result['data']['data'];
        // 根据国家代码获取国家名称
        $country = Db::name("admin_globalcode")->where('two_code',$data['userInfo']['country'])->value('china_name');
        $province = '';
        $city = '';
        if($data['userInfo']['country'] == 'CN'){
            $province_data = Db::name("admin_area")->where(['pinyin'=>lcfirst($data['userInfo']['province']),'level'=>2])->find();
            if(!empty($province_data)){
                $province = $province_data['shortname'];
                $city = Db::name("admin_area")->where(['pinyin'=>lcfirst($data['userInfo']['city']),'level'=>3,'pid' => $province_data['id']])->value('shortname');
            }
        }
        try {
            $check_wx->isUpdate(true)->save([
                'country' => $country,
                'province' => $province,
                'city' => $city,
                'sex' => $data['userInfo']['sex'],
                'snsbgImg' => $data['userInfoExt']['snsUserInfo']['snsBGImgID'],
            ]);
            return [
                'code' => 200,
                'data' => '',
                'msg' => '保存成功',
            ];
        } catch (\Exception $e) {
            return [
                'code' => 400,
                'data' => '',
                'msg' => $e->getMessage(),
            ];
        }
    }

    /**
     * 初始化好友列表
     * 返回json
     * @param $guid
     * @param $wxid
     * @return array|\think\response\Json
     */
    public function WXInitContact(Request $request)
    {
        $guid = $request->param('guid');
        $wxid = $request->param('wxid');
        $api = '/api/Contact/WXInitContact';
        $params = [
            "Guid"=> $guid,
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200){
            return json([
                'code' => 400,
                'data' => '',
                'msg' => '初始化通讯录失败'
            ]);
        }
        // 获取全部好友wxid(暂时为100人)
        $data = $result['data']['data']['contactUsernameList'];
        if(empty($data)){
            return json([
                'code' => 400,
                'data' => '',
                'msg' => '初始化通讯录失败'
            ]);
        }
        try{
            // 每次获取20个好友详细信息
            $new_data = array_chunk($data,20);
            foreach ($new_data as $val){
                $res = $this->WXGetContact($guid,$wxid,$val);
                if($res['code'] != 200){
                    return json([
                        'code' => $res['code'],
                        'msg' => $res['msg'],
                    ]);
                }
            }
            return json([
                'code' => 200,
                'data' => count($data),
                'msg' => '请求成功,更新'.count($data)."好友",
            ]);
        } catch (\Exception $e) {
            return json([
                'code' => 400,
                'data' => '',
                'msg' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 获取联系人详细信息
     * 返回数组
     * @param $guid
     * @param $array_wxid
     * @return array
     */
    public function WXGetContact($guid,$wxid,$array_wxid)
    {
        $api = '/api/Contact/WXGetContact';
        $params = [
            "Guid"=> $guid,
            "Users"=> $array_wxid,
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200 || empty($result['data']['data'])){
            return [
                'code' => 400,
                'data' => '',
                'msg' => '获取用户信息失败'
            ];
        }
        $array_data = $result['data']['data']['contactList'];
        try {
            $data = [];
            foreach ($array_data as $val){
                $wx_contactinfo = new ScContactinfoModel();
                // 判断是否有群昵称
                if(empty($val['chatRoomOwner'])){
                    // 根据国家代码获取国家名称
                    $country = Db::name("admin_globalcode")->where('two_code',$val['country'])->value('china_name');
                    $province = '';
                    $city = '';
                    if($val['country'] == 'CN'){
                        $province_data = Db::name("admin_area")->where(['pinyin'=>lcfirst($val['province']),'level'=>2])->find();
                        if(!empty($province_data)){
                            $province = $province_data['shortname'];
                            $city = Db::name("admin_area")->where(['pinyin'=>lcfirst($val['city']),'level'=>3,'pid' => $province_data['id']])->value('shortname');
                        }
                    }
                    $wx_info = $wx_contactinfo->where(['wxid'=>$val['userName']['string'],'type'=>1])->find();
                    $new_data = [
                        'wxid' => $val['userName']['string'],
                        'nickname' => $this->emoji_encode($val['nickName']['string']),
                        'headimgurl' => $val['bigHeadImgUrl'],
                        'alias' => $val['alias'],
                        'country' => $country,
                        'province' => $province,
                        'city' => $city,
                        'sex' => $val['sex'],
                        'type' => 1,
                        'py' => $val['pyInitial']['string'],
                    ];
                    $data[$val['userName']['string']] = $new_data;
                    if(empty($wx_info)){
                        $wx_contactinfo->save($new_data);
                    } else {
                        $wx_info->isUpdate(true)->save($new_data);
                    }
                    $wx_friend = new ScFriendModel();
                    $check = $wx_friend->where(['ownerwxid'=>$wxid,'friendwxid'=>$val['userName']['string']])->find();
                    if(empty($check)){
                        $wx_friend->save([
                            'ownerwxid' => $wxid,
                            'friendwxid' => $val['userName']['string'],
                            'remark' => $this->emoji_encode($val['remark']['string']),
                            'remarkpy' => $val['remarkPYInitial']['string'],
                            'is_delete' => 2,
                        ]);
                    } else {
                        if($check['is_delete'] == 1){
                            $check->isUpdate(true)->save([
                                'remark' => $this->emoji_encode($val['remark']['string']),
                                'remarkpy' => $val['remarkPYInitial']['string'],
                                'is_delete' => 2,
                            ]);
                        }
                    }
                } else {
                    // 保存群信息
                    $wx_info = $wx_contactinfo->where(['wxid'=>$val['userName']['string'],'type'=>2])->find();
                    $new_data = [
                        'wxid' => $val['userName']['string'],
                        'nickname' => $this->emoji_encode($val['nickName']['string']),
                        'headimgurl' => $val['smallHeadImgUrl'],
                        'type' => 2,
                        'py' => $val['pyInitial']['string'],
                    ];
                    $data[$val['userName']['string']] = $new_data;
                    if(empty($wx_info)){
                        $wx_contactinfo->save($new_data);
                    } else {
                        $wx_info->isUpdate(true)->save($new_data);
                    }
                    $wx_friend = new ScFriendModel();
                    $check = $wx_friend->where(['ownerwxid'=>$wxid,'friendwxid'=>$val['userName']['string']])->find();
                    if(empty($check)){
                        $wx_friend->save([
                            'ownerwxid' => $wxid,
                            'friendwxid' => $val['userName']['string'],
                            'remark' => $this->emoji_encode($val['remark']['string']),
                            'remarkpy' => $val['remarkPYInitial']['string'],
                            'is_delete' => 2,
                        ]);
                    } else {
                        if($check['is_delete'] == 1){
                            $check->isUpdate(true)->save([
                                'remark' => $this->emoji_encode($val['remark']['string']),
                                'remarkpy' => $val['remarkPYInitial']['string'],
                                'is_delete' => 2,
                            ]);
                        }
                    }
                    // 获取群成员信息
                    $this->WXGetChatroomMemberDetail($guid,$val['userName']['string']);
                }
            }
            return [
                'code' => 200,
                'data' => $data,
                'msg' => "请求成功",
            ];
        } catch (\ Exception $e){
            return [
                'code' => 400,
                'data' => '',
                'msg' => $e->getMessage(),
            ];
        }
    }

    /**
     * 获取群成员信息
     * @param $guid
     * @param $chatroom
     * @return array
     */
    public function WXGetChatroomMemberDetail($guid,$chatroom)
    {
        $api = '/api/Chatroom/WXGetChatroomMemberDetail';
        $params = [
            "Guid"=> $guid,
            "Chatroom"=> $chatroom,
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200 || empty($result['data']['data'])){
            return [
                'code' => 400,
                'data' => '',
                'msg' => '获取用户信息失败'
            ];
        }
        $data = $result['data']['data']['newChatroomData']['chatRoomMember'];
        foreach ($data as $val){
            $wx_friend = new ScFriendModel();
            $check = $wx_friend->where(['ownerwxid'=>$chatroom,'friendwxid'=>$val['userName']])->find();
            if(empty($check)){
                $wx_friend->save([
                    'ownerwxid' => $chatroom,
                    'friendwxid' => $val['userName'],
                    'remark' => $this->emoji_encode($val['displayName']),
                    'remarkpy' => '',
                    'is_delete' => 2,
                ]);
            } else {
                $check->isUpdate(true)->save([
                    'remark' => $this->emoji_encode($val['displayName']),
                    'remarkpy' => '',
                    'is_delete' => 2,
                ]);
            }
            $array[] = $val['userName'];
            $wx_contactinfo = new ScContactinfoModel();
            $wx_info = $wx_contactinfo->where(['wxid'=>$val['userName'],'type'=>1])->find();
            $new_data = [
                'wxid' => $val['userName'],
                'nickname' => $this->emoji_encode($val['nickName']),
                'headimgurl' => $val['bigHeadImgUrl'],
                'type' => 3, // 群成员信息,没有详细信息
            ];
            if(empty($wx_info)){
                $wx_contactinfo->save($new_data);
            } else {
                $wx_info->isUpdate(true)->save($new_data);
            }
        }
    }

    /**
     * 发送消息
     * @param $type
     * @param $guid
     * @param $data
     * @return array
     */
    public function WXSendMsg($type,$guid,$data)
    {
        $params = [
            "Guid"=> $guid,
            "UserName"=> $data['partyid'],
        ];
        // 消息类型 1:文字; 3:图片;34:语音;43:视频;47:动画表情,49:分享;
        switch ($type){
            case 1:
                // 文本
                $api = '/api/Message/WXSendMsg';
                $params["Content"] = $data['content'];// 文本内容
                break;
            case 3: // 图片
                $api = '/api/Message/WXSendImage';
                $params["Base64Image"] = str_replace('data:image/png;base64,','',$data['content']);; // 图片base64
                break;
            case 34:
                // 语音(未实现)
                $api = '/api/Message/WXSendVoice';
                $params["Type"] = ""; //音频格式：AMR = 0, MP3 = 2, SILK = 4, SPEEX = 1, WAVE = 3
                $params["DataFileBase64"] = ""; //音频文件BASE64
                $params["VoiceTime"] = ""; // 音频时长，单位毫秒
                break;
            case 43:
                // 视频(未实现)
                $api = '/api/Message/WXSendVideo';
                $params["VideoFileBase64"] = ""; //视频文件BASE64
                $params["ImageFileBase64"] = ""; //缩略图文件BASE64
                $params["VideoTime"] = ""; //视频时长
                break;
            case 47: // 动画表情(未实现)
                break;
            case 49:
                // 分享(未实现)
                $api = '/api/Message/WXSendShare';
                $params["Title"] = ""; //标题
                $params["Description"] = ""; //描述
                $params["Type"] = ""; //app类型 3：音乐 4：小app 5：大app
                $params["Url"] = ""; //链接地址
                $params["DataUrl"] = ""; //数据链接地址
                $params["ThumbUrl"] = ""; //缩略图链接地址
                break;
            default:
                return[
                    'code' => 100,
                    'msg' => '发送类型错误'
                ];
                break;
        }
        $result = $this->requestCurl($api,$params);
        if($result['code'] != 200){
            return [
                'code' => 400,
                'data' => '',
                'msg' => $result['msg']
            ];
        }
        if(empty($result['data']['data'])){
            return [
                'code' => 400,
                'data' => '',
                'msg' => '发送失败'
            ];
        }
        $newmsgid = 0;
        switch ($type){
            case 1:
                // 文本
                $newmsgid = $result['data']['data']['List'][0]['newMsgId'];
                break;
            case 3:
                // 图片
                $newmsgid = $result['data']['data']['Newmsgid'];
                break;
            case 34:
                // 语音(未实现)
                break;
            case 43:
                // 视频(未实现)
                break;
            case 47:
                // 动画表情(未实现)
                break;
            case 49:
                // 分享(未实现)
                break;
            default:
                return[
                    'code' => 100,
                    'msg' => '发送类型错误'
                ];
                break;
        }
        return [
            'code' => 200,
            'data' => $newmsgid,
            'msg' => '发送成功'
        ];
    }

    /**
     * 同步消息
     * @param $guid
     * @param string $wxid
     * @return array
     */
    public function WXSyncMsg($guid,$wxid='')
    {
        $api = '/api/Message/WXSyncMsg';
        $params = [
            "Guid"=> $guid,
        ];
        $result = $this->requestCurl($api,$params);

        if($result['code'] != 200){
            return [
                'code' => 400,
                'data' => '',
                'msg' => '同步消息连接失败'
            ];
        }
        $data = $result['data']['data']['Result']['AddMsgs'];
        $Ret = $result['data']['data']['Ret'];
        if($Ret == '-1'){
            return [
                'code' => 202,
                'data' => '',
                'msg' => '登录失效'
            ];
        }
        if(empty($data)){
            return [
                'code' => 201,
                'data' => '',
                'msg' => '无'
            ];
        }
        $num = 0;
        $status = 0;
        $msg = '';
        foreach ($data as $key => $val){
            $MsgType = $val['MsgType'];
            // 过滤无用类型
            $no_type = ['51','10002'];
            if(in_array($MsgType,$no_type)){
                continue;
            }
            if($val['FromUserName']['String'] == $wxid){
                // 同步消息 我方为发送人
                $is_send = 1;
                $ourid = $val['FromUserName']['String'];
                $partyid = $val['ToUserName']['String'];
            } else {
                // 同步消息 我方接受消息
                $is_send = 2;
                $ourid = $val['ToUserName']['String'];
                $partyid = $val['FromUserName']['String'];
            }
            $data = [
                'ourid' => $ourid,
                'partyid' => $partyid,
                'newmsgid' => $val['NewMsgId'],
                'type' => $MsgType,
                'is_send' => $is_send,
                'send_time' => $val['CreateTime'],
            ];
            // 区别个人消息  群消息
            $send_type = Db::name("sc_contactinfo")->where("wxid",$partyid)->value("type");
            // 群消息写入发送人wxid
            if($send_type == 2){
                $str = $val['Content']['String'];
                $isMatched_two = preg_match('/(.*?)(?=:\\n)/', $str, $matches_two);
                if($isMatched_two != 1){
                    $matches_two[0] = '';
                }
                $data['sp_send'] = $matches_two[0];
            }
            switch ($MsgType){
                // 消息类型 1:文字; 3:图片;34:语音;43:视频;47:动画表情;49;分享;
                case 1:
                    //文本
                    $data['content'] = $this->emoji_encode($val['Content']['String']);
                    if($send_type == 2){
                        $isMatched_two = preg_match('/(?<=:\\n)(.*)/', $val['Content']['String'], $matches_two);
                        if($isMatched_two != 1){
                            $matches_two[0] = '';
                        }
                        $data['content'] = $this->emoji_encode($matches_two[0]);
                    }
                    break;
                case 3:
                    // 图片
                    $data['content'] = '[图片]';
                    $data['img_base64'] = 'data:image/png;base64,'.$val['ImgBuf']['Buffer'];
                    break;
                case 34:
                    //语音(未实现)
                    $data['content'] = '[语音]';
                    break;
                case 43:
                    //视频(未实现)
                    $data['content'] = '[视频]';
                    break;
                case 47:
                    //图片表情
                    $data['content'] = '[动画表情]';
                    $str = $val['Content']['String'];
                    $isMatched = preg_match('/(?<=cdnurl=\\")(.*?)(?=\\")/', $str, $matches);
                    if($isMatched != 1){
                        $matches[0] = '';
                    }
                    $data['img_base64'] = $matches[0];
                    break;
                case 49:
                    //分享(未实现)
                    $data['content'] = '[分享]';
                    break;
            }
            try {
                // 保存聊天记录
                (new ScChatrecordModel())->save($data);
                // 保存聊天关联信息
                $chatassociationModel = new ScChatassociationModel();
                $chatassociation = $chatassociationModel->where(['ourid' => $ourid, 'partyid' => $partyid,])->find();
                if(empty($chatassociation)){
                    $chatassociationModel->save([
                        'ourid' => $ourid,
                        'partyid' => $partyid,
                        'status' => 1,
                        'time'=>time(),
                    ]);
                } else {
                    $chatassociation->isUpdate(true)->save([
                        'time'=>time(),
                        'status' => 1
                    ]);
                }
                // 获取用户信息
                $ScContactinfo = (new ScContactinfoModel())->where('wxid',$partyid)->find();
                $array_wxid = [
                    $partyid,
                ];
                if(empty($ScContactinfo)){
                    $this->WXGetContact($guid,$ourid,$array_wxid);
                }
                $status = 1;
                $num++;
            } catch (\Exception $e){
                $status = 2;
                $msg = $e->getMessage();
            }
        }
        if($status == 1){
            return [
                'code' => 200,
                'data' => $num,
                'msg' => 'OK'
            ];
        } else {
            return [
                'code' => 400,
                'data' => '',
                'msg' => $msg
            ];
        }
    }

    /**
     * 请求微信API
     * @param $api  // api接口
     * @param $data // 参数
     * @return mixed
     */
    function requestCurl($api,$data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://47.111.9.5:81".$api);
//        curl_setopt($curl, CURLOPT_URL, "http://81.71.38.54:7777".$api);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_POST, true);
        $header[] = 'Content-type:application/json';
        $header[] = 'accept:text/plain';
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $result = curl_exec($curl);
        if(curl_getinfo($curl, CURLINFO_HTTP_CODE) != 200){
            return [
                'code'  => 400,
                'data'  => '',
                'msg' => '请求失败'
            ];
        }
        curl_close($curl);
        return [
            'code'  => 200,
            'data'  => json_decode($result,true),
            'msg' => '请求成功'
        ];
    }

    /**
     * 表情转换（进行编码）
     * @param $nickname
     * @return string
     * 解码 rawurldecode($nickname)
     */
    function emoji_encode($nickname){
        $strEncode = '';
        $length = mb_strlen($nickname,'utf-8');
        for ($i=0; $i < $length; $i++) {
            $_tmpStr = mb_substr($nickname,$i,1,'utf-8');
            if(strlen($_tmpStr) >= 4){
                $strEncode .= rawurlencode($_tmpStr);
            }else{
                $strEncode .= $_tmpStr;
            }
        }
        return $strEncode;
    }

    /**
     *消息撤回
     * @param $UserName
     * @param $NewMsgId
     * @param $Guid
     * @return mixed|string
     *
     */
    public function WXRevokeMsg($UserName,$NewMsgId,$Guid){
        $api = '/api/Message/WXRevokeMsg';
        $params= [
            'UserName'=> $UserName,
            'NewMsgId'=>$NewMsgId,
            'Guid'=>$Guid
        ];
        $result = $this->requestCurl($api,$params);
        //dump($result);echo 1;die;
        if($result['code'] != 200){
            return '未知操作';
        }
        return $result['data']['data'];
    }

    /**
     * 注销微信
     * @param $guid
     * @return array
     *
     */
    public function WxLogOut($guid){
        //dump($guid);die;
        $api="/api/Login/WXLogout";
        $params = [
            "guid"=>$guid
        ];
        $result = $this->requestCurl($api,$params);
        if($result['code'] == 200){
            return [
              'code'=>'200',
              'msg'=>$result['msg']
            ];
        }else{
            return [
                'code'=>'0',
                'msg'=>$result['msg']
            ];
        }
    }

}
