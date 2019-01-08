<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Cache;
use DB;
class GroupController extends Controller
{
    /**
     * @author woann<304550409@qq.com>
     * @param Request $request
     * @return GroupController
     * @des 获取群成员
     */
    public function groupMember(Request $request)
    {
        $id = $request->get('id');
        $list = DB::table('group_member as gm')
            ->leftJoin('user as u','u.id','=','gm.user_id')
            ->select('u.username','u.id','u.avatar','u.sign')
            ->where('group_id', $id)
            ->get();
        if (!count($list)) {
            return $this->json(500,"获取群成员失败");
        }
        return $this->json(0,"",['list' => $list]);
    }

    /**
     * @author woann<304550409@qq.com>
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @des 查找页面
     */
    public function find(Request $request)
    {
        $type = $request->get('type');
        $wd = $request->get('wd');
        $user_list = [];
        $group_list = [];
        switch ($type) {
            case "user" :
                $user_list = DB::table('user')->select('id','nickname','avatar')->where('id','like','%'.$wd.'%')->orWhere('nickname','like','%'.$wd.'%')->orWhere('username','like','%'.$wd.'%')->get();
                break;
            case "group" :
                $group_list = DB::table('group')->select('id','groupname','avatar')->where('id','like','%'.$wd.'%')->orWhere('groupname','like','%'.$wd.'%')->get();
                break;
            default :
                break;
        }
        return view('find',['user_list' => $user_list,'group_list' => $group_list,'type' => $type,'wd' => $wd]);
    }

    /**
     * @author woann<304550409@qq.com>
     * @param Request $request
     * @return GroupController
     * @des 加入群
     */
    public function joinGroup(Request $request)
    {
        $session = session('user');
        $id = $request->post('groupid');
        $isIn = DB::table('group_member')->where('group_id',$id)->where('user_id', $session->user_id)->first();
        if ($isIn) {
            return $this->json(500,"您已经是该群成员");
        }
        $group = DB::table('group')->find($id);
        $res = DB::table('group_member')->insert(['group_id' => $id,'user_id' => $session->user_id]);
        if (!$res) {
            return $this->json(500,"加入群失败");
        }
        $data = [
            "type" => "group",
            "avatar"    => $group->avatar,
            "groupname" =>$group->groupname,
            "id"        =>$group->id
        ];
        return $this->json(200,"加入成功",$data);
    }

    /**
     * @author woann<304550409@qq.com>
     * @param Request $request
     * @return GroupController|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @des 创建群
     */
    public function createGroup(Request $request)
    {
        if($request->isMethod("POST")){
            $session = session('user');
            $post = $request->post();
            $data = [
                'groupname' => $post['groupname'],
                'user_id'   => $session->user_id,
                'avatar'    => $post['avatar']
            ];
            DB::beginTransaction();
            $group_id = DB::table('group')->insertGetId($data);
            $res_join = DB::table('group_member')->insert(['group_id' => $group_id,'user_id' => $session->user_id]);
            if ($group_id && $res_join) {
                DB::commit();
                $data = [
                    "type" => "group",
                    "avatar"    => $post['avatar'],
                    "groupname" => $post['groupname'],
                    "id"        => $group_id
                ];
                return $this->json(200,"创建成功！",$data);
            } else {
                DB::callback();
                return $this->json(500,"创建失败！");
            }
        }else{
           return view('create_group');
        }
    }

}
