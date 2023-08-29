# IM即时聊天

#### 介绍
Raingad-IM是一个开源的即时通信demo，需要前后端配合使用，主要用于学习交流，为大家提供即时通讯的开发思路，许多功能需要自行开发，开发的初衷旨在快速建立企业内部通讯系统、内网交流、社区交流。

前端地址：https://gitee.com/raingad/im-chat-front

后端地址：https://gitee.com/raingad/im-instant-chat

体验地址：http://im.raingad.com/index.html

账号：13800000002  密码：123456

尾号2、3、4......18、19、20 都是

账号：13800000020  密码：123456 

#### 支持功能

 1. 支持单聊和群聊，支持发送表情、图片、语音、视频和文件消息

 2. 单聊支持消息已读未读的状态显示，在线状态显示

 3. 群聊创建、删除和群成员管理、群公告、群禁言等

 4. 支持置顶联系人，消息免打扰；支持设置新消息声音提醒，浏览器通知

 5. 支持一对一音视频通话（已打通web端和移动端）

 6. 支持文件、图片和绝大部分媒体文件在线预览

 7. 支持移动端（H5、APP和小程序，部分功能不兼容），支持简易后台管理

 8. 全新支持企业模式和社区模式，社区模式支持注册、添加好友功能


> 移动端版本已经上线，请关注演示地址内的链接。


#### 最新更新
**v-2.8.25** (2023年8月25日)
1. 优化移动端的websocket连接
2. 移动端新增记住密码
3. 优化消息接收的处理以及上下线状态
4. 移动端完善用户资料修改，密码修改等
5. 打通移动端和web端的音视频互通，有bug，但是可以忽略，已开源web端音视频源码
6. 新增音视频通话消息类型

**v-2.8.1** (2023年8月1日)
1. 移动端新增消息撤回、复制，消息的发送状态，1对1消息已读、未读状态
2. 移动端修复聊天页面中，消息不能滚动到底部的情况
3. 移动端新增音视频通话引导用户获取音视频权限
4. web端新增截屏功能，功能用的第三方插件，虽然有瑕疵，但是很强大
5. 优化websocket掉线问题，增加客户端主动心跳
6. 优化web端浏览器通知
7. 移动端支持社区模式，加好友等功能
8. 新增邮件加密方式选择
9. 兼容php7.4
10. 修复element-ui图标有时候加载乱码


**v-2.7.14** (2023年7月14日)
1. 支持简易后台管理
2. 全新支持企业模式和社区模式，可自由切换
3. 新增阿里云、七牛云、腾讯云等对象储存
4. 新增群头像自动生成
5. 新增人员资料查看
6. 新增文件管理，可以快速发送到聊天。
7. 新增移动端1对1音视频通话，不和web端互通


**v-2.5.30** (2023年5月20日)
1. 新增windows系统的支持，建议windows仅用于开发环境，正式环境请使用linux。
2. 新增企业模式下全局发送消息的演示页面。


**v-1.10.30** (2022年10月30日)
1. 升级vue-cli2到vue-cli3
2. 优化发送按键和换行键
3. 新增语音消息、视频消息
4. 新增支持音视频通话（peerjs）
5. 使用sass依赖替代node-sass（这东西太坑了）


**v-0.1.0** (2021年04月24日)
1. 项目建立，只是有项目需要，才开发了这个应用

#### 软件架构

后端技术栈：`thinkphp6+workerman+redis`

前端技术栈：`vue2+Lemon-IMUI+element-UI`


#### 安装教程
##### 第一种方式
1.  克隆代码到本地： 
``` 
git clone https://gitee.com/raingad/im-instant-chat.git
```
2.  进入项目目录，执行： 
```
composer install
```
3.  进入 public\sql\database.sql 将数据库导入自己的数据库。

4.  进入项目根目录，修改 `example.env` 为 `.env` ，并修改数据库相应的参数。

##### 第二种方式
1. 下载完整源码放到自己的服务器上。请注意看 `右侧的发行版`，请在发行版中下载最新发布的版本。
2. 开启伪静态，下面只展示nginx的，Apache的自己百度
``` 
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
```
3. 访问你的ip或者域名即可进入自定义安装向导。

PS：如需开启聊天文件存入oss，需要在后台中进行配置，配置后不要再对环境配置文件进行修改。

#### 启动消息推送服务
因为是聊天软件需要用到websockt，所以我们需要启动workerman，系统已经内置了相应的服务，可以在后台管理首页进行运行服务，但是首次使用需要先进行调试。

1. 进入项目根目录 运行 `php think worker:gateway start -d`，或者运行 `php start.php start -d` 即可运行消息服务，windows下请直接运行根目录下的`start_for_win.bat`文件，由于Workerman在Windows下有诸多使用限制，所以正式环境建议用Linux系统，windows系统仅建议用于开发环境。

2. 消息服务需要放行 8282 端口，如需修改，请修改 [ `app\worker\start_gateway.php`] 中的 8282 端口。端口号根据情况需改，如果修改了端口号，需要将前端的程序修改并重新打包上传到项目的public目录下。
   
3. 系统采用直接用域名作为websocket服务的地址，所以监听端口需要在网站的nginx中配置代理并监听8282端口。

```
   location /wss
    {
      proxy_pass http://127.0.0.1:8282;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "Upgrade";
      proxy_set_header X-Real-IP $remote_addr;
    }
```

4. 更多关于workerman的使用，请进入[workerman官网](https://www.workerman.net/)官网进行查阅。

5. 部署完成之后管理员账号密码为：`administrator`  `123456`.

#### 安装部署服务

服务器要求：
|  所需环境 | 版本 | 备注 |
| --------- | ---- | ---- |
| linux    | >= 7.0 |  以下的版本未做测试   |
| php | >= 7.1 |  不兼容8    |
| mysql    | >= 5.7 | 必须要5.7     |
| redis    | >= 5.0 |     |
| workerman    | >= 4.0 |  用于消息服务部署  |

作者提供本系统的安装服务，包括后端和前端部署到线上，保证项目的完美运行，200元/次，安装服务可赠送web端音视频通话源码，如有需要可以进群联系作者！

#### 交流群
如果有什么问题，请留言，或者加入我们的QQ群！

创作不易，点个star吧

[QQ 交流群：336921267](https://jq.qq.com/?_wv=1027&k=jMQAt9lh)

