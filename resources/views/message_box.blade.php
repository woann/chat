<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>消息盒子</title>
    <link rel="stylesheet" href="/asset/layuiv2/css/layui.css" media="all">
    <style>
        .layim-msgbox{margin: 15px;}
        .layim-msgbox li{position: relative; margin-bottom: 10px; padding: 0 130px 10px 60px; padding-bottom: 10px; line-height: 22px; border-bottom: 1px dotted #e2e2e2;}
        .layim-msgbox .layim-msgbox-tips{margin: 0; padding: 10px 0; border: none; text-align: center; color: #999;}
        .layim-msgbox .layim-msgbox-system{padding: 0 10px 10px 10px;}
        .layim-msgbox li p span{padding-left: 5px; color: #999;}
        .layim-msgbox li p em{font-style: normal; color: #FF5722;}

        .layim-msgbox-avatar{position: absolute; left: 0; top: 0; width: 50px; height: 50px;}
        .layim-msgbox-user{padding-top: 5px;}
        .layim-msgbox-content{margin-top: 3px;}
        .layim-msgbox .layui-btn-small{padding: 0 15px; margin-left: 5px;}
        .layim-msgbox-btn{position: absolute; right: 0; top: 12px; color: #999;}
    </style>
</head>
<body>
<ul class="layim-msgbox" id="LAY_view">
    @foreach($list as $k=>$v)
        @if($v->type == 0)
            <li data-uid="{{ $v->uid }}" data-fromgroup="{{ $v->group_id }}">
                <a href="javascript:;">
                    <img style="width: 40px;height: 40px" src="{{ $v->avatar }}" class="layui-circle layim-msgbox-avatar"></a>
                <p class="layim-msgbox-user">
                    <a href="javascript:;" >{{ $v->nickname }}</a>
                    <span>{{ $v->time }}</span></p>
                <p class="layim-msgbox-content">申请添加你为好友
                    <span>附言: {{ $v->remark }}</span></p>
                <p class="layim-msgbox-btn">
                    @if($v->status == 0)
                    <button class="layui-btn layui-btn-small" onclick="agree({{ $v->id }},$(this),'{{ $v->avatar }}','{{ $v->nickname }}')">同意</button>
                    <button class="layui-btn layui-btn-small layui-btn-primary" onclick="refuse({{ $v->id }},$(this))">拒绝</button>
                    @else
                        <span>已{{ $v->status == 1 ? '同意' : '拒绝' }}</span>
                    @endif
                </p>
            </li>
        @else
            <li class="layim-msgbox-system">
                <p>
                    <em>系统：</em>{{ $v->nickname }} 已经{{ $v->status == 1 ? '同意' : '拒绝' }}你的好友申请
                    <span>{{ $v->time }}</span></p>
            </li>
        @endif
    @endforeach

    <div class="layui-flow-more">
        <li class="layim-msgbox-tips">暂无更多新消息</li>
    </div>
</ul>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/asset/layui/layui.js"></script>
<script>
    var layer;
    layui.use('layer', function(){
        layer = layui.layer;
    });
    function refuse(id,obj) {
        $.ajax({
            url : "/refuse_friend",
            type: "post",
            data: {id:id},
            dataType:"json",
            success:function (res) {
                if (res.code == 200){
                    layer.msg(res.msg)
                    //如果成功了，发出socket消息，通知被拒绝者
                    obj.parent().html('<span>已拒绝</span>');
                    parent.sendMessage(parent.socket,JSON.stringify({type:"refuseFriend",id:id}))
                }else{
                    layer.msg(res.msg,function(){})
                }
            },
            error: function () {
                layer.msg("网络繁忙",function(){})
            }
        });
    }
    function agree(id,obj,avatar,nickname){
        parent.layui.layim.setFriendGroup({
            type: 'friend'
            ,username: nickname //好友昵称，若申请加群，参数为：groupname
            ,avatar: avatar //头像
            ,group: parent.layui.layim.cache().friend //获取好友列表数据
            ,submit: function(group, index){
                parent.layer.close(index); //关闭改面板
                $.ajax({
                    url:"/add_friend",
                    type:"post",
                    data:{id:id,groupid:group},
                    dataType:"json",
                    success:function (res) {
                        console.log(res)
                        console.log(res.code)
                        //执行添加好友操作
                        if (res.code == 200){
                            uid = obj.parents('li').attr('data-uid');
                            fromgroup = obj.parents('li').attr('data-fromgroup');
                            parent.sendMessage(parent.socket, JSON.stringify({type:"addList",id:uid,fromgroup:fromgroup}))//通知对方，我已同意，把我加入到对方好友列表并添加消息提醒
                            parent.layui.layim.addList(res.data); //将刚通过的好友追加到好友列表
                        } else {
                            layer.msg(res.msg,function(){});
                        }
                    },
                    error:function () {
                        layer.msg("网络繁忙",function(){});
                    }
                })
            }
        });
    }
</script>
</body>
</html>