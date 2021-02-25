<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\View;
use think\Request;

class SmartCloudController extends Controller
{
    /**
     * 微信列表
     *
     * @return string
     */
    public function index()
    {
        $sc_user = Db('sc_user')->paginate(10)->each(function($item, $key){
            $item['nickname'] = rawurldecode($item['nickname']);
            return $item;
        });
        return View::fetch("smartcloud/index",[
            'list' => $sc_user,
        ]);
    }

    /**
     * 联系人列表
     * @param Request $request
     * @return string
     * @throws \think\exception\DbException
     */
    public function FriendList(Request $request)
    {
        $wxid = $request->param('id');
        $type = $request->param('type');
        $nickname = $request->param('nickname');
        $sc_contactinfo_db = Db('sc_contactinfo');
        if(!empty($wxid)){
            $sc_contactinfo_db->alias('a')
                ->join('sc_friend b','b.friendwxid = a.wxid')
                ->where("b.ownerwxid = '".$wxid."'");
        }
        $sc_contactinfo = $sc_contactinfo_db
            ->where([
                ['nickname','like','%'.$nickname.'%']
            ])
            ->order('type','desc')
            ->group('wxid')
            ->paginate(10,false,['query' => $request->param()])
            ->each(function($item, $key){
                $item['nickname'] = rawurldecode($item['nickname']);
                return $item;
            });
        if($type == 2){
            $owner_name = Db::name('sc_contactinfo')->where('wxid',$wxid)->value('nickname');
        } else {
            $owner_name = Db::name('sc_user')->where('wxid',$wxid)->value('nickname');
        }
        return View::fetch("smartcloud/friendlist",[
            'list' => $sc_contactinfo,
            'owner_name' => rawurldecode($owner_name),
            'wxid' => $wxid,
        ]);
    }

    /**
     * 查看聊天记录
     * @param Request $request
     * @return string
     * @throws \think\exception\DbException
     */
    public function FriendChatRecord(Request $request)
    {
        $wxid = $request->param('id');
        $friend_wxid = $request->param('friend');
        $chatrecord = Db('sc_user')
            ->alias('a')
            ->join('sc_chatrecord c','a.wxid = c.ourid')
            ->join('sc_contactinfo d','d.wxid = "'.$friend_wxid.'"')
            ->where([
                'c.ourid' => $wxid,
                'c.partyid' => $friend_wxid
            ])
            ->field('a.nickname as a_nickname,c.*,d.nickname as d_nickname')
            ->order('c.send_time','desc')
            ->paginate(10,false,['query' => $request->param()])
            ->each(function($item, $key){
                $item['a_nickname'] = rawurldecode($item['a_nickname']);
                if(!empty($item['sp_send'])){
                    $item['d_nickname'] = rawurldecode(Db::name('sc_contactinfo')->where('wxid',$item['sp_send'])->value('nickname'));
                } else {
                    $item['d_nickname'] = rawurldecode($item['d_nickname']);
                }
                $item['content'] = rawurldecode($item['content']);
                return $item;
            });
        $owner_name = Db::name('sc_user')->where('wxid',$wxid)->value('nickname');
        $friend_name = Db::name('sc_contactinfo')->where('wxid',$friend_wxid)->value('nickname');
        return View::fetch("smartcloud/friendchatrecord",[
            'list' => $chatrecord,
            'owner_name' => rawurldecode($owner_name),
            'friend_name' => rawurldecode($friend_name),
        ]);
    }
}
