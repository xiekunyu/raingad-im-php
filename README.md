# IM即时聊天

#### 介绍
IM后端代码，需要配合前端使用，本软件主要用于学习交流，开发的初衷旨在快速建立企业内部通讯系统，不能用于商业用途。

前端传送门：https://gitee.com/raingad/im-chat-front

体验地址：http://im.raingad.com/index.html

账号：13800000002  密码：123456

······2-9都是

账号：13800000009  密码：123456

#### 支持功能

- 单聊和群聊
- 支持发送表情、图片和文件
- 支持消息复制、撤回（仅支持两分钟内）、转发
- 单聊支持消息已读未读的状态显示
- 支持设置新消息提醒
- 支持部分Lemon-imui内功能设置
- 支持文件(需要第三方应用支持)和图片在线预览
- 群聊创建、删除和群成员管理、群公告等
#### 软件架构

后端技术栈：`thinkphp6+workerman`

前端技术栈：`vue+Lemon-IMUI+element-UI`


#### 安装教程
##### 第一种方式
1.  克隆代码到本地： 
``` 
git clone https://gitee.com/raingad/im-instant-chat.git
```
2.  进入项目目录，执行： 
```
composer intsall
```
3.  进入 public\sql\database.sql 将数据库导入自己的数据库。

4.  进入项目根目录，修改 `example.env` 为 `.env` ，并修改数据库相应的参数。

##### 第二种方式
1. 下载完整源码放到自己的服务器上[下载地址](https://gitee.com/raingad/im-instant-chat/releases/0.6.14)。
2. 开启伪静态，下面只展示nginx的，Apache的自己百度
``` 
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
```
3. 访问你的ip或者域名即可进入自定义安装向导。

PS：如需开启聊天文件存入oss，需要在 `.env` 中填写 `[OSS]` 参数信息，不填写默认使用本地文件系统。如果需要文件预览服务，还需填写 `[PREVIEW]` 一栏，具体查看 `example.env` 的配置说明。

#### 启动消息推送服务
因为是聊天软件需要用到websockt，所以我们需要启动workerman。

1. 进入项目根目录 运行 `./start.sh`，或者运行 `php start.php start -d` 即可运行消息服务。

2. 消息服务需要放行 8282 端口，如需修改，请修改 [ `app\push\start_gateway.php`] 中的 8282 端口。端口号根据情况需改，如果修改了端口号，需要将前端的程序修改并重新打包上传到项目的public目录下。

3. 更多关于workerman的使用，请进入[workerman官网](https://www.workerman.net/)官网进行查阅。


#### 交流群
如果有什么问题，请留言，或者加入我们的QQ群！

[QQ 交流群：336921267](https://jq.qq.com/?_wv=1027&k=jMQAt9lh).

