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
        var_dump($user);
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
                'username'  => $user->username.'('.$user->id.')',
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
        $session = session('user');
        $from_user_id = $request->post('from_user_id');
        $isFriend = DB::table('friend')->where('user_id',$session->user_id)->where('friend_id',$from_user_id)->first();
        if ($isFriend) {
            return $this->json(500,'已经是好友了');
        }
        $data = [
            [
                'user_id' => $session->user_id,
                'friend_id' =>$from_user_id,
                'friend_group_id' => $request->post('from_friend_group_id')
            ],
            [
                'user_id' =>$from_user_id,
                'friend_id' => $session->user_id,
                'friend_group_id' => $request->post('to_friend_group_id')
            ]
        ];
        $res = DB::table('friend')->insert($data);
        if (!$res) {
            return $this->json(500,'添加失败');
        }
        return $this->json(200,'添加成功');
    }

}
