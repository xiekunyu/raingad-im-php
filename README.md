简体中文 | [English](./README.en.md)

# IM即时聊天

### 介绍
Raingad-IM是一个开源的即时通信demo，需要前后端配合使用，主要用于学习交流，为大家提供即时通讯的开发思路，许多功能需要自行开发，开发的初衷旨在快速建立企业内部通讯系统、内网交流、社区交流。

|  类型 | 链接 |
| --------- | ---- |
| 前端源码    | https://gitee.com/raingad/im-chat-front |
| 后端源码 | https://gitee.com/raingad/im-instant-chat |
| web端演示 | http://im.raingad.com/index.html |
| 移动端H5演示 | http://im.raingad.com/h5 |
| 安卓APP演示 | https://emoji.raingad.com/file/raingad.apk |


体验账号：13800000002  密码：123456

尾号2、3、4......18、19、20 都是

体验账号：13800000020  密码：123456 

### 支持功能

- 支持单聊和群聊，支持发送表情、图片、语音、视频和文件消息
- 单聊支持消息已读未读的状态显示，在线状态显示
- 群聊创建、删除和群成员管理、群公告、群禁言、@群成员等
- 支持置顶联系人，消息免打扰；
- 支持设置新消息声音提醒，浏览器通知
- 支持管理员撤回群成员消息，支持群成员不能互相添加好友
- 支持一对一音视频通话（已打通web端和移动端，小程序不支持）
- 支持文件、图片和绝大部分媒体文件在线预览
- 支持移动端（由uniapp开发，可打包H5、APP和小程序）
- 全新支持企业模式和社区模式，社区模式支持注册、添加好友功能
- APP支持单聊消息在线、离线推送（需要自行申请unipush服务）
- 支持简易后台管理，包括用户管理、群组管理、系统设置等

### 最新更新
请查看右侧发行版更新日志

**v4.2.0**

1. 接入三方（thinkapi）敏感词检测系统，可以自由开启
2. 增加简易消息引用，但是点击引用消息无法跳转
3. 增加电脑端消息图片复制功能
4. 修复社区模式下无法搜索联系人
5. 优化移动端输入框和录音
6. 修复若干bug

### 软件架构

后端技术栈：`thinkphp6+workerman+redis`

前端技术栈：`vue2+Lemon-IMUI+element-UI`

桌面端：`vue2+Lemon-IMUI+element-UI + electron`

移动端：`uniapp for vue3 + pinia`

### 安装教程

<b style="color:red">由于即时通讯的特殊性，严禁将源码用于木马、病毒、色情、赌博、诈骗等违反国家法律法规的行业，以及从事违法犯罪活动，如发现有使用本软件进行非法活动，将向有关部门举报和协助相关行政执法机关清查！</b>

> 本安装教程主要是服务端的安装，请认真仔细按照安装教程来，不要自作主张根据自己的想法来。请结合各个项目中根目录的项目说明文件`README.md`进行相应端的开发和发布。

> 安装程序需要有一定的PHP经验和服务器运维经验，如果没有请加入交流群联系作者，作者提供付费部署服务！

#### 准备工作

需要先安装好运行环境，推荐使用宝塔服务器，安装LNMP的架构，建议使用nginx作为服务器，不建议使用apache。需要安装以下软件：
|  所需环境 | 版本 | 备注 | 推荐版本 |
| --------- | ---- | ---- | ---|
| linux    | >= 7.0 |  以下的版本未做测试   | 7.9 |
| nginx    | >= 1.17 |     | 最新的 |
| php | >= 7.1 |  不兼容8    | 7.3 |
| mysql    | >= 5.7 | 必须要5.7及以上     | 5.7 |
| redis    | >= 5.0 |     | 7.0 |

**重要操作**
>使用宝塔面板更易安装

<b style="color:red">以下内容，非常重要！！！！！如果出现错误，重新检查是否以满足条件。</b>

1、PHP需要安装扩展：`redis` `fileinfo`

2、PHP需要取消禁用函数：`shell_exec` `proc_open` `pcntl_exec` `pcntl_alarm` `pcntl_fork` `pcntl_waitpid` `pcntl_wait` `pcntl_signal` `pcntl_signal_dispatch` `putenv`

<b style="color:red">以上内容，非常重要！！！！！如果出现错误，重新检查是否以满足条件。</b>

#### 源码下载

下载发行版【推荐】
- 【推荐使用】下载完整源码放到自己的服务器上。请注意看gitee项目主页顶部右侧 [发行版链接（戳我）](https://gitee.com/raingad/im-instant-chat/releases) ，请在发行版中下载最新发布的版本。



#### 开始安装
1. 把代码上传至服务器，将整个目录权限给 `www` 用户，并赋予 `755`权限，创建网站，把网站的运行目录指向项目根目录下的 `public` 目录，运行目录一定要是这个。

2. 设置伪静态和反向代理，下面只展示nginx的伪静态和反向代理配置，apache的请自行百度或者使用chatGPT转换。【直接复制下面全部代码到伪静态中即可】


``` 
#伪静态配置
location ~* (runtime|application)/{
	return 403;
}
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}

#反向代理配置，如果有修改端口，需要替换下方的8282端口

location /wss
    {
      proxy_pass http://127.0.0.1:8282;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "Upgrade";
      proxy_set_header X-Real-IP $remote_addr;
    }
```

3. 如果有域名并且要使用音视频通话、语音消息等服务还需要配置证书来开启HTTPS，可以使用免费的 `Let's Encrypt` 证书，如果不需要这些服务，可以直接使用HTTP协议和IP地址访问，但是功能会受限。
   
4. 访问你的ip或者域名即可进入自定义安装向导，如果访问出错，就是自己的准备工作没有按照要求进行，请检查。

5. 部署完成之后管理员账号密码为：`administrator`  `123456`，管理入口在聊天界面的左下角。
   
6. 安装成功后是无法实时接收消息的，请参考下一章“启动消息推送服务”。

#### 如果安装失败
1.  进入 `public\sql\database.sql` 将数据库导入自己的数据库。

2.  进入项目根目录，修改 `example.env` 为 `.env` ，并修改数据库相应的参数，**请仔细阅读env中的配置说明**。

PS：如需开启聊天文件存入oss，需要在后台中进行配置，配置后不要再对环境配置文件进行修改。

### 启动消息推送服务
因为是聊天软件需要用到websockt，所以我们需要启动workerman，系统已经内置了相应的服务，可以在后台管理首页 **（管理员账号登录后，从左下角进入）** 进行运行服务。如果后台运行启动不成功，需要进行以下调试：

**系统服务启动失败的原因：**

1. 在终端启动了 `php think worker:gateway start` 或者 `php start.php start` 这两个命令，请在终端中运行 ` killall -9 php ` 或者重启启动服务器再运行。

2. 可能是php不是默认版本，终端执行 `php -v` 查看版本号是否和创建网站是选定的php版本不一致，如果不一致，需要把网站的版本修改为默认的php版本，并php安装对应的依赖和取消禁用函数。

3. 可能是执行的目录权限不够。重新对所有目录设置为 用户 `www` 权限 `755` ，再次重试。

**如果启动失败可进行调试**

1. 进入项目根目录 运行 `php think worker:gateway start`，或者运行 `php start.php start` 即可运行消息服务。windows下请直接运行根目录下的`start_for_win.bat`文件，由于Workerman在Windows下有诸多使用限制，所以正式环境建议用Linux系统，windows系统仅建议用于开发环境。如果没有出现报错，请将运行的命令终止，或者执行`php think worker:gateway stop` or `php start.php stop`，自己启动了那个就终止哪个。调试了没有问题的话，可以直接在管理后台启动消息推送服务

2. 消息服务需要放行 8282 端口，如需修改，请修改环境配置文件中`WORKER` 板块的相应参数。windows用户请修改 [ `app\worker\start_gateway.php`] 中的 8282 端口。端口号根据情况需改。
   
3. 系统采用直接用域名作为websocket服务的地址，所以需要在网站的nginx中配置代理并监听8282端口，已在伪静态中写了代理配置的参数。

4. 更多关于workerman的使用，请进入[workerman官网](https://www.workerman.net/)官网进行查阅。


#### 视频教程（无声）

[哟，原来有视频教程！！！](https://im.file.raingad.com/static/video/jiaocheng.mp4)

### 安装部署服务

作者提供本系统的安装服务，包括后端和前端部署到线上，保证项目的完美运行，200元/次，安装服务可赠送详细的安装教程以及接口文档，如有需要可以进群联系作者！

### 交流群
如果有什么问题，请留言，或者加入我们的QQ群！

创作不易，点个star吧，【请先点Star后才可以申请加入交流群，否则不予通过】

[QQ 交流群：336921267](https://jq.qq.com/?_wv=1027&k=jMQAt9lh)
