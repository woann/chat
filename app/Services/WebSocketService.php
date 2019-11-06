<?php
// +----------------------------------------------------------------------
// | Created by PhpStorm
// +----------------------------------------------------------------------
// | Date: 19-1-7 下午2:48
// +----------------------------------------------------------------------
// | Author: woann <304550409@qq.com>
// +----------------------------------------------------------------------
namespace App\Services;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use DB;
/**
 * @see https://wiki.swoole.com/wiki/page/400.html
 */
class WebSocketService implements WebSocketHandlerInterface
{
    private $sessionid = null;
    // 声明没有参数的构造函数
    public function __construct()
    {
    }
    public function sendByUid($server,$uid,$data,$offline_msg = false)
    {
        $fd = app('swoole')->wsTable->get('uid:'.$uid);//获取接受者fd
        if ($fd == false){
            //这里说明该用户已下线，日后做离线消息用
            if ($offline_msg) {
                $data = [
                    'user_id'   => $uid,
                    'data'      => json_encode($data),
                ];
                //插入离线消息
                DB::table('offline_message')->insert($data);
            }
            return false;
        }
        return $server->push($fd['value'], json_encode($data));//发送消息
    }
    /**
     * @author woann<304550409@qq.com>
     * @param \swoole_websocket_server $server
     * @param \swoole_http_request $request
     * @des 链接开启时
     */
    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        //判断是否传递了sessionid参数
        if(!isset($request->get["sessionid"])){
            $data = [
                "type" => "token expire"
            ];
            $server->push($request->fd, json_encode($data));
            return;
        }
        $sessionid = $request->get["sessionid"];//获取sessionid
        session()->setId($sessionid);//赋值sessionid
        session()->start();//开启session
        $session = session('user');//获取session中信息
        $this->sessionid = $sessionid;
        var_dump($this->sessionid);
        if($session == null){
            $data = [
                "type" => "token expire"
            ];
            $server->push($request->fd, json_encode($data));
            return;
        }
        //绑定fd变更状态
        app('swoole')->wsTable->set('uid:' . $session->user_id, ["value"=>$request->fd]);// 绑定uid到fd的映射
        app('swoole')->wsTable->set('fd:' . $request->fd,["value"=>$session->user_id]);// 绑定fd到uid的映射
        DB::table('user')->where('id', $session->user_id)->update(['status' => 'online']);//标记为在线
        //给好友发送上线通知，用来标记头像去除置灰
        $friend_list = DB::table('friend')->where('user_id',$session->user_id)->get();
        $data = [
            "type"  => "friendStatus",
            "uid"   => $session->user_id,
            "status"=> 'online'
        ];
        foreach ($friend_list as $k => $v) {
            $this->sendByUid($server,$v->friend_id,$data);
        }
        //获取未读消息盒子数量
        $count = DB::Table('system_message')->where('user_id',$session->user_id)->where('read',0)->count();
        $data = [
            "type"      => "msgBox",
            "count"     => $count
        ];
        //检查离线消息
        $offline_messgae = DB::table('offline_message')->where('user_id', $session->user_id)->where('status', 0)->get();
        foreach ($offline_messgae as $k=>$v) {
            $res = $this->sendByUid($server,$session->user_id,json_decode($v->data));
            if ($res) {
                //如果推送成功标记当前离线消息为已发送
                DB::table('offline_message')->where('id', $v->id)->update(['status' => 1]);
            }
        }
        $server->push($request->fd, json_encode($data));

    }

    /**
     * @author woann<304550409@qq.com>
     * @param \swoole_websocket_server $server
     * @param \swoole_websocket_frame $frame
     * @des 接收到消息
     */
    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        $info = json_decode($frame->data);//接受收到的数据并转为object
        $sessionid = $this->sessionid;//获取sessionid
        session()->setId($sessionid);//赋值sessionid
        session()->start();//开启session
        $session = session('user');//获取session中信息
        if($sessionid == null || $session == null){
            $data = [
                "type" => "token_expire"
            ];
            $server->push($frame->fd, json_encode($data));
        }
        if (!isset($info->type)) {
            return;
        }
        //根据type字段判断消息类型并执行对应操作
        switch ($info->type) {
            //心跳包
            case "ping":
                break;
            //聊天消息
            case "chatMessage":
                if ($info->data->to->type == "friend") {
                    //好友消息
                    $data = [
                        'username' => $info->data->mine->username,
                        'avatar' => $info->data->mine->avatar,
                        'id' => $info->data->mine->id,
                        'type' => $info->data->to->type,
                        'content' => $info->data->mine->content,
                        'cid' => 0,
                        'mine'=> $session->user_id == $info->data->to->id ? true : false,//要通过判断是否是我自己发的
                        'fromid' => $info->data->mine->id,
                        'timestamp' => time()*1000
                    ];
                    if ($info->data->to->id == $session->user_id) {
                        return;
                    }
                    $this->sendByUid($server,$info->data->to->id,$data,true);
                    //记录聊天记录
                    $record_data = [
                        'user_id'       => $info->data->mine->id,
                        'friend_id'     => $info->data->to->id,
                        'group_id'      => 0,
                        'content'       => $info->data->mine->content,
                        'time'    => time()
                    ];
                    DB::table('chat_record')->insert($record_data);
                } elseif ($info->data->to->type == "group") {
                    //群消息
                    $data = [
                        'username' => $info->data->mine->username,
                        'avatar' => $info->data->mine->avatar,
                        'id' => $info->data->to->id,
                        'type' => $info->data->to->type,
                        'content' => $info->data->mine->content,
                        'cid' => 0,

                        'mine'=> false,//要通过判断是否是我自己发的
                        'fromid' => $info->data->mine->id,
                        'timestamp' => time()*1000
                    ];
                    $list = DB::table('group_member as gm')
                        ->leftJoin('user as u','u.id','=','gm.user_id')
                        ->select('u.id')
                        ->where('group_id', $info->data->to->id)
                        ->get();
                    foreach ($list as $k => $v) {
                        if ( $v->id == $session->user_id) {
                            continue;
                        }
                        $this->sendByUid($server,$v->id,$data,true);
                    }
                    //记录聊天记录
                    $record_data = [
                        'user_id'       => $info->data->mine->id,
                        'friend_id'     => 0,
                        'group_id'      => $info->data->to->id,
                        'content'       => $info->data->mine->content,
                        'time'    => time()
                    ];
                    DB::table('chat_record')->insert($record_data);
                }
                break;
            //发送好友请求
            case "addFriend":
                $friend_id = $info->to_user_id;
                $system_message_data = [
                    'user_id'   => $friend_id,//接受者
                    'from_id'   => $session->user_id,//来源者
                    'remark'    => $info->remark,
                    'type'      => 0,
                    'group_id'  => $info->to_friend_group_id,
                    'time'      => time()
                ];
                $isFriend = DB::table('friend')->where('friend_id',$friend_id)->where('user_id',$session->user_id)->first();
                if ($isFriend) {
                    $data = [
                        'type' => 'layer',
                        'code' => 500,
                        'msg'   => '对方已经是你的好友，不可重复添加'
                    ];
                    $this->sendByUid($server,$session->user_id,$data);
                    return;
                }
                if ($friend_id == $session->user_id){
                    $data = [
                        'type' => 'layer',
                        'code' => 500,
                        'msg'   => '不能添加自己为好友'
                    ];
                    $this->sendByUid($server,$session->user_id,$data);
                    return;
                }
                DB::table('system_message')->insert($system_message_data);
                //获取该接受者未读消息数量
                $count = DB::Table('system_message')->where('user_id',$friend_id)->where('read',0)->count();
                $data = [
                    "type"      => "msgBox",
                    "count"     => $count
                ];
                $this->sendByUid($server,$friend_id,$data,true);
                break;
            //追加好友到好友列表
            case "addList":
                $user = DB::table('user')->find($session->user_id);
                $data = [
                    "type" => "addList",
                    "data" => [
                        "type"  => "friend",
                        "avatar"    => $user->avatar,
                        "username" => $user->nickname,
                        "groupid" => $info->fromgroup,
                        "id"        => $user->id,
                        "sign"    => $user->sign
                    ]
                ];
                //获取未读消息盒子数量
                $count = DB::Table('system_message')->where('user_id',$info->id)->where('read',0)->count();
                $data1 = [
                    "type"      => "msgBox",
                    "count"     => $count
                ];
                $this->sendByUid($server,$info->id,$data);
                $this->sendByUid($server,$info->id,$data1,true);
                break;
            case "refuseFriend":
                $id = $info->id;//消息id
                $system_message = DB::table('system_message')->find($id);
                //获取该接受者未读消息数量
                $count = DB::Table('system_message')->where('user_id',$system_message->from_id)->where('read',0)->count();
                $data = [
                    "type"      => "msgBox",
                    "count"     => $count
                ];
                $this->sendByUid($server,$system_message->from_id,$data);
                break;
            case "joinNotify":
                $groupid = $info->groupid;
                $list = DB::table('group_member')->where('group_id',$groupid)->get();
                $data = [
                    "type" => "joinNotify",
                    "data"  => [
                        "system"    => true,
                        "id"        => $groupid,
                        "type"      => "group",
                        "content"   => $session->nickname."加入了群聊，欢迎下新人吧～"
                    ]
                ];
                foreach ($list as $k=>$v) {
                    $this->sendByUid($server,$v->user_id,$data);
                }
                break;
            default:
                break;
        }
    }
    public function onClose(\swoole_websocket_server $server, $fd, $reactorId)
    {
        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
        $uid = app('swoole')->wsTable->get('fd:' . $fd);
        $friend_list = DB::table('friend')->where('user_id',$uid['value'])->get();
        $data = [
            "type"  => "friendStatus",
            "uid"   => $uid['value'],
            "status"=> 'offline'
        ];
        foreach ($friend_list as $k => $v) {
            $this->sendByUid($server,$v->friend_id,$data);
        }
        if ($uid !== false) {
            app('swoole')->wsTable->del('uid:' . $uid['value']);// 解绑uid映射
        }
        app('swoole')->wsTable->del('fd:' . $fd);// 解绑fd映射
        DB::table('user')->where('id',$uid)->update(['status' => 'offline']);
        session(['user'=>null]);//释放session
        $this->session = null;
    }
}