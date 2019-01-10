<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>woann-chat</title>
    <link rel="stylesheet" href="/asset/layui/css/layuiv2.css" media="all">
</head>
<body>
<ul class="layui-nav" >
    <li class="layui-nav-item" style="float: right;">
        <a href="javascript:;"><img src="{{ session('user')->avatar }}" class="layui-nav-img">{{ session('user')->username }}</a>
        <dl class="layui-nav-child">
            <dd><a href="/loginout">退出登录</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item layui-this"><a href="/">首页</a></li>
    <li class="layui-nav-item"><a href="https://www.woann.cn">吴先生的博客</a></li>
    <li class="layui-nav-item"><a href="https://github.com/woann">Github</a></li>
</ul>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/asset/layui/layui.js"></script>
<script>
        var socket;
        var ping;
        function sendMessage(socket, data){
            var readyState = socket.readyState;
            console.log("连接状态码："+readyState);
            socket.send(data)
        }
        layui.use('element', function(){
            var element = layui.element;
        });
        layui.use('layim', function(layim){
            //基础配置
            layim.config({
                init: {
                    url: '/userinfo' //接口地址（返回的数据格式见下文）
                    ,type: 'get' //默认get，一般可不填
                    ,data: {} //额外参数
                }
                //获取群员接口（返回的数据格式见下文）
                ,members: {
                    url: '/group_members' //接口地址（返回的数据格式见下文）
                    ,type: 'get' //默认get，一般可不填
                    ,data: {} //额外参数
                }
                //上传图片接口（返回的数据格式见下文），若不开启图片上传，剔除该项即可
                ,uploadImage: {
                    url: '/upload?type=im_image&path=im' //接口地址
                    ,type: 'post' //默认post
                }
                //上传文件接口（返回的数据格式见下文），若不开启文件上传，剔除该项即可
                ,uploadFile: {
                    url: '/upload?type=im_file&path=file' //接口地址
                    ,type: 'post' //默认post
                }
                //扩展工具栏，下文会做进一步介绍（如果无需扩展，剔除该项即可）
                ,tool: [{
                    alias: 'code' //工具别名
                    ,title: '代码' //工具名称
                    ,icon: '&#xe64e;' //工具图标，参考图标文档
                }]
                ,msgbox: '/message_box' //消息盒子页面地址，若不开启，剔除该项即可
                ,find: '/find'//发现页面地址，若不开启，剔除该项即可
                ,chatLog: '/chat_log' //聊天记录页面地址，若不开启，剔除该项即可
            });
            //监听自定义工具栏点击，以添加代码为例
            //建立websocket连接
            socket = new WebSocket('ws://127.0.0.1:9501?sessionid={{ $sessionid }}');
            socket.onopen = function(){
                console.log("websocket is connected")
                ping = setInterval(function () {
                    sendMessage(socket,'{"type":"ping"}');
                    console.log("ping...");
                },1000 * 10)
            };
            socket.onmessage = function(res){
                console.log('接收到数据'+ res.data);
                data = JSON.parse(res.data);
                switch (data.type) {
                    case "friend":
                    case "group":
                        layim.getMessage(data); //res.data即你发送消息传递的数据（阅读：监听发送的消息）
                        break;
                    case "layer":
                        if (data.code == 200) {
                            layer.msg(data.msg)
                        } else {
                            layer.msg(data.msg,function(){})
                        }
                        break;
                    case "addList":
                        console.log(data.data)
                        layim.addList(data.data);
                        break;
                    case "friendStatus" :
                        console.log(data.status)
                        layim.setFriendStatus(data.uid, data.status);
                        break;
                    case "msgBox" :
                        //为了等待页面加载，不然找不到消息盒子图标节点
                        setTimeout(function(){
                            if(data.count > 0){
                                layim.msgbox(data.count);
                            }
                        },1000);
                        break;
                    case "token_expire":
                        window.location.reload();
                        break;

                }
            };
            socket.onclose = function(){
                console.log("websocket is closed")
                clearInterval(ping)
            }
            layim.on('sendMessage', function(res){
                var mine = res.mine; //包含我发送的消息及我的信息
                var to = res.to; //对方的信息
                sendMessage(socket,JSON.stringify({
                    type: 'chatMessage' //随便定义，用于在服务端区分消息类型
                    ,data: res
                }));
            });
            layim.on('sign', function(value){
                console.log(value); //获得新的签名
                $.ajax({
                    url:"/update_sign",
                    type:"post",
                    data:{sign:value},
                    dataType:"json",
                    success:function (res) {
                        if(res.code == 200){
                            layer.msg(res.msg)
                        }else{
                            layer.msg(res.msg,function () {})
                        }
                    },
                    error:function () {
                        layer.msg("网络繁忙",function(){});
                    }
                })
            });
            layim.on('tool(code)', function(insert, send, obj){ //事件中的tool为固定字符，而code则为过滤器，对应的是工具别名（alias）
                layer.prompt({
                    title: '插入代码'
                    ,formType: 2
                    ,shade: 0
                }, function(text, index){
                    layer.close(index);
                    insert('[pre class=layui-code]' + text + '[/pre]'); //将内容插入到编辑器，主要由insert完成
                    //send(); //自动发送
                });
            });

        });

</script>
</body>
</html>
