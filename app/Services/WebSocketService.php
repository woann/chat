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
    // 声明没有参数的构造函数
    public function __construct()
    {
    }

    /**
     * @author woann<304550409@qq.com>
     * @param \swoole_websocket_server $server
     * @param \swoole_http_request $request
     * @des 链接开启时
     */
    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        //判断session是否为空
        $session = session('user');
        if($session == null){
            $data = [
                "type" => "token expire"
            ];
            $server->push($request->fd, json_encode($data));
        }
        app('swoole')->wsTable->set('uid:' . $session->user_id, ["value"=>$request->fd]);// 绑定uid到fd的映射
        app('swoole')->wsTable->set('fd:' . $request->fd,["value"=>$session->user_id]);// 绑定fd到uid的映射
        DB::table('user')->where('id', $session->user_id)->update(['status' => 'online']);//标记为在线
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
        $session = session('user');
        if($session == null){
            $data = [
                "type" => "token_expire"
            ];
            $server->push($frame->fd, json_encode($data));
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
                    $fd = app('swoole')->wsTable->get('uid:'.$info->data->to->id);
                    if ($fd == false || $info->data->to->id == $session->user_id) {
                        return;
                    }
                    $server->push($fd['value'], json_encode($data));
                } elseif ($info->data->to->type == "group") {
                    //群消息
                    $data = [
                        'username' => $info->data->mine->username,
                        'avatar' => $info->data->mine->avatar,
                        'id' => $info->data->to->id,
                        'type' => $info->data->to->type,
                        'content' => $info->data->mine->content,
                        'cid' => 0,
                        'mine'=> $session->user_id == $info->data->to->id ? true : false,//要通过判断是否是我自己发的
                        'fromid' => $info->data->mine->id,
                        'timestamp' => time()*1000
                    ];
                    $list = DB::table('group_member as gm')
                        ->leftJoin('user as u','u.id','=','gm.user_id')
                        ->select('u.id')
                        ->where('group_id', $info->data->to->id)
                        ->get();
                    foreach ($list as $k => $v) {
                        $fd = app('swoole')->wsTable->get('uid:'.$v->id);
                        if ($fd == false || $v->id == $session->user_id) {
                            continue;
                        }
                        $server->push($fd['value'], json_encode($data));
                    }
                }
                break;
            //添加好友
            case "addFriend":
                $friend_id = $info->to_user_id;
                $user = DB::table('user')->find($session->user_id);
                $data = [
                    "type" => "addFriend",
                    "data" => [
                        "type"  => "friend",
                        "avatar"    => $user->avatar,
                        "username" => $user->nickname,
                        "groupid" => $info->to_friend_group_id,
                        "id"        => $user->id,
                        "sign"    => $user->sign
                    ]
                ];
                $data2 = [
                    'user_id'   => $info->to_user_id,
                    'from_id'   => $session->userid,
                    'remark'    =>$info->remark,
                ];
                DB::table('system_message')->insert($data2);
//                $fd = app('swoole')->wsTable->get('uid:'.$friend_id);
//                $server->push($fd['value'], json_encode($data));
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
                        "groupid" => $info->groupid,
                        "id"        => $user->id,
                        "sign"    => $user->sign
                    ]
                ];
                $fd = app('swoole')->wsTable->get('uid:'.$info->id);
                $server->push($fd['value'], json_encode($data));
                break;
            default:
                break;
        }
    }
    public function onClose(\swoole_websocket_server $server, $fd, $reactorId)
    {
        // throw new \Exception('an exception');// 此时抛出的异常上层会忽略，并记录到Swoole日志，需要开发者try/catch捕获处理
        $uid = app('swoole')->wsTable->get('fd:' . $fd);
        if ($uid !== false) {
            app('swoole')->wsTable->del('uid:' . $uid['value']);// 解绑uid映射
        }
        app('swoole')->wsTable->del('fd:' . $fd);// 解绑fd映射
        DB::table('user')->where('id',$uid)->update(['status' => 'offline']);
    }
}