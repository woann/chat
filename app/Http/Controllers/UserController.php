<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Cache;
use DB;
class UserController extends Controller
{
    /**
     * @author woann<304550409@qq.com>
     * @return UserController
     * @des 初始化用户信息
     */
    public function userinfo()
    {
        $session = session('user');
        $user = DB::table('user')->find($session->user_id);
        if (!$user) {
            return $this->json(500,"获取用户信息失败");
        }
        $groups = DB::table('group_member as gm')
            ->leftJoin('group as g','g.id','=','gm.group_id')
            ->select('g.id','g.groupname','g.avatar')
            ->where('gm.user_id', $user->id)->get();
        foreach ($groups as $k=>$v) {
            $groups[$k]->groupname = $v->groupname.'('.$v->id.')';
        }
        $friend_groups = DB::table('friend_group')->select('id','groupname')->where('user_id', $user->id)->get();
        foreach ($friend_groups as $k => $v) {
            $friend_groups[$k]->list = DB::table('friend as f')
                ->leftJoin('user as u','u.id','=','f.friend_id')
                ->select('u.nickname as username','u.id','u.avatar','u.sign','u.status')
                ->where('f.user_id',$user->id)
                ->where('f.friend_group_id',$v->id)
                ->orderBy('status','DESC')
                ->get();
        }
        $data = [
            'mine'      => [
                'username'  => $user->nickname.'('.$user->id.')',
                'id'        => $user->id,
                'status'    => $user->status,
                'sign'      => $user->sign,
                'avatar'    => $user->avatar
            ],
            "friend"    => $friend_groups,
            "group"     => $groups
        ];
        return $this->json(0,'',$data);

    }

    /**
     * @author woann<304550409@qq.com>
     * @param Request $request
     * @return UserController
     * @des 添加好友
     */
    public function addFriend(Request $request)
    {
        $id = $request->post('id');
        $system_message = DB::table('system_message')->find($id);
        $isFriend = DB::table('friend')->where('user_id',$system_message->user_id)->where('friend_id',$system_message->from_id)->first();
        if ($isFriend) {
            return $this->json(500,'已经是好友了');
        }
        $data = [
            [
                'user_id' => $system_message->user_id,
                'friend_id' =>$system_message->from_id,
                'friend_group_id' => $request->post('groupid')
            ],
            [
                'user_id' =>$system_message->from_id,
                'friend_id' => $system_message->user_id,
                'friend_group_id' => $system_message->group_id
            ]
        ];
        $res = DB::table('friend')->insert($data);
        if (!$res) {
            return $this->json(500,'添加失败');
        }
        DB::table('system_message')->where('id',$id)->update(['status' => 1]);
        $user = DB::table('user')->find($system_message->from_id);
        $data = [
            "type"  => "friend",
            "avatar"    => $user->avatar,
            "username" => $user->nickname,
            "groupid" => $request->post('groupid'),
            "id"        => $user->id,
            "sign"    => $user->sign
        ];
        $system_message_data = [
            'user_id'   => $system_message->from_id,
            'from_id'   => $system_message->user_id,
            'type'      => 1,
            'status'    => 1,
            'time'      => time()
        ];
        $res1 = DB::table('system_message')->insert($system_message_data);
        return $this->json(200,'添加成功',$data);
    }

    public function refuseFriend(Request $request)
    {
        $id = $request->post('id');
        $system_message = DB::table('system_message')->find($id);
        DB::beginTransaction();
        $res = DB::table('system_message')->where('id',$id)->update(['status' => 2]);
        $data = [
            'user_id'   => $system_message->from_id,
            'from_id'   => $system_message->user_id,
            'type'      => 1,
            'status'    => 2,
            'time'      => time()
        ];
        $res1 = DB::table('system_message')->insert($data);
        if ($res && $res1){
            DB::commit();
            return $this->json(200,"已拒绝");
        } else {
            DB::callback();
            return $this->json(500,"操作失败");
        }
    }

    public function updateSign(Request $request)
    {
        $session = session('user');
        $sign = $request->post('sign');
        $res = DB::table('user')->where('id', $session->user_id)->update(['sign' => $sign]);
        if (!$res) {
            return $this->json(500,'签名修改失败');
        }
        return $this->json(200,'签名修改成功');
    }

}
