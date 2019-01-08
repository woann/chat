<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>聊天记录</title>
    <link rel="stylesheet" href="/asset/layui/css/layui.css" media="all">
    <style>
        body .layim-chat-main{height: auto;}
    </style>
</head>
<body>
<div class="layim-chat-main">
    <ul id="LAY_view">
        <li class="layim-chat-mine">
            <div class="layim-chat-user">
                <img src="//tva3.sinaimg.cn/crop.0.0.512.512.180/8693225ajw8f2rt20ptykj20e80e8weu.jpg">
                <cite>
                    <i>2016-12-05 08:31:22</i>纸飞机</cite>
            </div>
            <div class="layim-chat-text">
                <img alt="[抱抱]" title="[抱抱]" src="https://res.layui.com/layui/src/images/face/25.gif">
                <img alt="[心]" title="[心]" src="https://res.layui.com/layui/src/images/face/47.gif">你好啊小美女</div></li>
        <li>
            <div class="layim-chat-user">
                <img src="//tva3.sinaimg.cn/crop.0.0.512.512.180/8693225ajw8f2rt20ptykj20e80e8weu.jpg">
                <cite>Z_子晴
                    <i>2016-12-05 08:31:32</i>
                </cite>
            </div>
            <div class="layim-chat-text">你没发错吧？
                <img alt="[微笑]" title="[微笑]" src="https://res.layui.com/layui/src/images/face/0.gif"></div></li>
        <li>
            <div class="layim-chat-user">
                <img src="//tva3.sinaimg.cn/crop.0.0.512.512.180/8693225ajw8f2rt20ptykj20e80e8weu.jpg">
                <cite>Z_子晴
                    <i>2016-12-05 08:31:38</i>
                </cite>
            </div>
            <div class="layim-chat-text">你是谁呀亲。。我爱的是贤心！我爱的是贤心！我爱的是贤心！重要的事情要说三遍~</div></li>
        <li>
            <div class="layim-chat-user">
                <img src="//tva3.sinaimg.cn/crop.0.0.512.512.180/8693225ajw8f2rt20ptykj20e80e8weu.jpg">
                <cite>Z_子晴
                    <i>2016-12-05 08:31:48</i>
                </cite>
            </div>
            <div class="layim-chat-text">注意：这些都是模拟数据，实际使用时，需将其中的模拟接口改为你的项目真实接口。
                <br>该模版文件所在目录（相对于layui.js）：
                <br>/css/modules/layim/html/chatlog.html</div></li>
    </ul>
</div>
<script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="/asset/layui/layui.js"></script>
<script>
    layui.use(['layim', 'laypage'],
        function() {

        });
</script>
</body>
</html>