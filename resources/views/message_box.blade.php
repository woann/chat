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
    <li data-uid="166488" data-fromgroup="0">
        <a href="/u/166488/" target="_blank">
            <img src="//q.qlogo.cn/qqapp/101235792/B704597964F9BD0DB648292D1B09F7E8/100" class="layui-circle layim-msgbox-avatar"></a>
        <p class="layim-msgbox-user">
            <a href="/u/166488/" target="_blank">李彦宏</a>
            <span>刚刚</span></p>
        <p class="layim-msgbox-content">申请添加你为好友
            <span>附言: 有问题要问</span></p>
        <p class="layim-msgbox-btn">
            <button class="layui-btn layui-btn-small" data-type="agree">同意</button>
            <button class="layui-btn layui-btn-small layui-btn-primary" data-type="refuse">拒绝</button></p>
    </li>
    <li data-uid="347592" data-fromgroup="0">
        <a href="/u/347592/" target="_blank">
            <img src="//q.qlogo.cn/qqapp/101235792/B78751375E0531675B1272AD994BA875/100" class="layui-circle layim-msgbox-avatar"></a>
        <p class="layim-msgbox-user">
            <a href="/u/347592/" target="_blank">麻花疼</a>
            <span>刚刚</span></p>
        <p class="layim-msgbox-content">申请添加你为好友
            <span>附言: 你好啊！</span></p>
        <p class="layim-msgbox-btn">
            <button class="layui-btn layui-btn-small" data-type="agree">同意</button>
            <button class="layui-btn layui-btn-small layui-btn-primary" data-type="refuse">拒绝</button></p>
    </li>
    <li class="layim-msgbox-system">
        <p>
            <em>系统：</em>雷军 拒绝了你的好友申请
            <span>10天前</span></p>
    </li>
    <li class="layim-msgbox-system">
        <p>
            <em>系统：</em>马小云 已经同意你的好友申请
            <span>10天前</span></p>
    </li>
    <li class="layim-msgbox-system">
        <p>
            <em>系统：</em>贤心 已经同意你的好友申请
            <span>10天前</span></p>
    </li>
    <div class="layui-flow-more">
        <li class="layim-msgbox-tips">暂无更多新消息</li></div>
</ul>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/asset/layui/layui.js"></script>
</body>
</html>