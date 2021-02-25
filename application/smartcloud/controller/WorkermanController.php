<?php

namespace app\smartcloud\controller;

use app\smartcloud\model\ScUserModel;
use think\worker\Server;
use Workerman\Lib\Timer;

class WorkermanController extends Server
{
    protected $socket = 'websocket://0.0.0.0:9512';
    protected $uidConnections = array();
    protected $count = 1;
    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        if(!isset($connection->uid))
        {
            $connection->wxid = $connection->uid = $data;
            // 获取guid
            $connection->guid = (new ScUserModel())->where('wxid',$connection->wxid)->value("guid");
            $this->uidConnections[$connection->uid] = $connection;
            $this->sendMessageByUid($connection->uid,'3'); // 连接成功
            $connect_time = time();
            $connection->timer_id = Timer::add(5, function($connection)
            {
                $wx_api = new WxApiController();
                $result = $wx_api->WXSyncMsg($connection->guid,$connection->wxid);
                $this->sendMessageByUid($connection->uid,json_encode($result));
            }, array($connection, $connect_time));
        }
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        $connection->send('1'); // 正在连接
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        // 删除定时器
        Timer::del($connection->timer_id);
        $connection->send('2'); // 断开连接
        $connection->close();
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }

    // 针对uid推送数据
    public function sendMessageByUid($uid, $message)
    {
        if(isset($this->uidConnections[$uid]))
        {
            $connection = $this->uidConnections[$uid];
            $connection->send($message);
        }
    }
}
