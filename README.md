> `woann-chat`是一个基于LaravelS和Layim编写的聊天系统。

项目地址：[https://github.com/woann/chat](https://github.com/woann/chat)

| 依赖 | 说明 |
| -------- | -------- |
| [PHP](https://secure.php.net/manual/zh/install.php) | `>= 7.2` `推荐7.2` |
| [Swoole](https://www.swoole.com/) | `>= 4.2.9` `从2.0.12开始不再支持PHP5` `推荐4.2.9+` |
| [LaravelS](https://github.com/hhxsv5/laravel-s) | `>=3.3.9 LaravelS是一个将swoole和laravel框架结合起来的胶水工具` |

## 声明
* 此项目是基于LaravelS作为服务端，所以在此之前，你要熟悉swoole、laravel、还有将他们完美结合的`LaravelS`[https://github.com/hhxsv5/laravel-s](https://github.com/hhxsv5/laravel-s)
* 前端部分是采用layui,在此郑重说明，layui中的im部分`layim`并不开源，仅供交流学习，请勿将此项目中的layim用作商业用途。
* 此项目持续开发中，欢迎有兴趣的朋友共同维护

## 安装
* 执行安装命令`git clone https://github.com/woann/chat`将项目克隆到本地
* 导入sql，项目根目录下有个`woann_chat.sql`文件，将该sql文件导入数据库即可
* 修改`.env`文件，按照你的数据库账号密码进行配置
* 运行laravelS `php bin/laravels start`
* 此时访问`127.0.0.1:9501`即可进入登录页面
* 测试账号 默认账号:`woann`,密码:`wqg951122`,其他测试账号：`test01` - `test04` 密码全是`123456`，当然你也可以自行注册。

## 配合nginx使用
* nginx配置文件
```nginx
map $http_upgrade $connection_upgrade {
    default upgrade;
    ''      close;
}
upstream laravels {
    # 通过 IP:Port 连接
    server 127.0.0.1:9501 weight=5 max_fails=3 fail_timeout=30s;
    # 通过 UnixSocket Stream 连接，小诀窍：将socket文件放在/dev/shm目录下，可获得更好的性能
    #server unix:/xxxpath/laravel-s-test/storage/laravels.sock weight=5 max_fails=3 fail_timeout=30s;
    #server 192.168.1.1:5200 weight=3 max_fails=3 fail_timeout=30s;
    #server 192.168.1.2:5200 backup;
    keepalive 16;
}
server {
    listen 80;
    # 别忘了绑Host哟
    server_name xxx.com;#在这里配置域名
    root /xxx/woann-chat/public;#在这里配置文件目录
    access_log /yyypath/log/nginx/$server_name.access.log;
    autoindex off;
    index index.html index.htm;
    # Nginx处理静态资源(建议开启gzip)，LaravelS处理动态资源。
    location / {
        try_files $uri @laravels;
    }
    # 当请求PHP文件时直接响应404，防止暴露public/*.php
    #location ~* \.php$ {
    #    return 404;
    #}
    # Http和WebSocket共存，Nginx通过location区分
    # !!! WebSocket连接时路径为/ws
    # Javascript: var ws = new WebSocket("ws://xxx.com/ws");
    location =/ws {
        # proxy_connect_timeout 60s;
        # proxy_send_timeout 60s;
        # proxy_read_timeout：如果60秒内被代理的服务器没有响应数据给Nginx，那么Nginx会关闭当前连接；同时，Swoole的心跳设置也会影响连接的关闭
        # proxy_read_timeout 60s;
        proxy_http_version 1.1;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        proxy_pass http://laravels;
    }
    location @laravels {
        # proxy_connect_timeout 60s;
        # proxy_send_timeout 60s;
        # proxy_read_timeout 60s;
        proxy_http_version 1.1;
        proxy_set_header Connection "";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Real-PORT $remote_port;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header Server-Protocol $server_protocol;
        proxy_set_header Server-Name $server_name;
        proxy_set_header Server-Addr $server_addr;
        proxy_set_header Server-Port $server_port;
        proxy_pass http://laravels;
    }
}
```
* 将`resources/view/index.blade.php`文件中简历websocket中的
```javascript
socket = new WebSocket('ws://127.0.0.1:9501?sessionid={{ $sessionid }}');
```
替换成
```javascript
socket = new WebSocket('ws://xxx.com/ws?sessionid={{ $sessionid }}');
```

## 待完成
* 离线消息
* ...

## 部分截图
* 主界面
![image.png](https://upload-images.jianshu.io/upload_images/9160823-0a98529381fb35be.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 收到消息
![image.png](https://upload-images.jianshu.io/upload_images/9160823-c94eabb2198f88c9.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 聊天界面
![image.png](https://upload-images.jianshu.io/upload_images/9160823-bac8b54de33b0677.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 添加好友
![image.png](https://upload-images.jianshu.io/upload_images/9160823-437b3e463d54bdc2.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 加入群
![image.png](https://upload-images.jianshu.io/upload_images/9160823-52f2c910912ee606.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 同意添加
![image.png](https://upload-images.jianshu.io/upload_images/9160823-f1b7f520e1d03a7b.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 群聊
![image.png](https://upload-images.jianshu.io/upload_images/9160823-28901e6c1ed6f234.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)


* 消息盒子
![image.png](https://upload-images.jianshu.io/upload_images/9160823-12f0e5e20739b12f.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)


* 聊天记录
![](https://upload-images.jianshu.io/upload_images/9160823-16319fd32a85ac96.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
