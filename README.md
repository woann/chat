> `woann-chat`是一个基于laravelS和layim编写的聊天系统。

项目地址：[https://github.com/woann/chat](https://github.com/woann/chat)
| 依赖 | 说明 |
| -------- | -------- |
| [PHP](https://secure.php.net/manual/zh/install.php) | `>= 7.2` `推荐7.2` |
| [Swoole](https://www.swoole.com/) | `>= 4.2.9` `从2.0.12开始不再支持PHP5` `推荐4.2.9+` |
| [LaravelS](https://github.com/hhxsv5/laravel-s) | `LaravelS是一个将swoole和laravel框架结合起来的胶水工具` |

## 声明
* 此项目是基于LaravelS作为服务端，所以在此之前，你要熟悉swoole、laravel、还有将他们完美结合的`LaravelS`[https://github.com/hhxsv5/laravel-s](https://github.com/hhxsv5/laravel-s)
* 前端部分是采用layui,在此郑重说明，layui中的im部分`layim`并不开源，仅供交流学习，请勿将此项目中的layim用作商业用途。
* 此项目持续开发中，欢迎有兴趣的朋友共同维护

## 安装
* 执行安装命令`git clone https://github.com/woann/chat`将项目克隆到本地
* 导入sql，项目根目录下有个`woann_chat.sql`文件，将该sql文件导入数据库即可
* 修改`.env`文件，按照你的数据库账号密码进行配置
* 运行laravelS `php bin/laravels start`
* 此时访问`127.0.0.1:9501`即可进入登录页面（默认账号:`woann`,密码:`wqg951122`）

## 部分截图
* 主界面
![image.png](https://upload-images.jianshu.io/upload_images/9160823-0f62484f5e3b6891.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
* 查找界面
![image.png](https://upload-images.jianshu.io/upload_images/9160823-01841a3135a6e2ce.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)


![image.png](https://upload-images.jianshu.io/upload_images/9160823-55c8e11c54a45cb4.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
* 群聊
![image.png](https://upload-images.jianshu.io/upload_images/9160823-337160b3880bc1d0.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)
* 聊天记录 （待开发）
![image.png](https://upload-images.jianshu.io/upload_images/9160823-070885583c4dcbb5.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

* 消息盒子（待开发）
![image.png](https://upload-images.jianshu.io/upload_images/9160823-4c4f8f1f71fb37de.png?imageMogr2/auto-orient/strip%7CimageView2/2/w/1240)

