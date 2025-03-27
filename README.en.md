[简体中文](./README.md) | English 

# Instant Messaging

### Introduce
Raingad-IM is an open source instant communication demo, which needs to be used together with the front and back ends. It is mainly used for learning and communication, and provides you with the development ideas of instant communication. Many functions need to be developed by itself, and the original intention of development is to quickly establish the internal communication system, Intranet communication and community communication.

|  type | url |
| --------- | ---- |
| Front-end source code    | https://gitee.com/raingad/im-chat-front |
| Back-end source code | https://gitee.com/raingad/im-instant-chat |
| Web Demo | http://im.raingad.com/index.html |
| H5 Demo | http://im.raingad.com/h5 |


Dmeo account：13800000002  password：123456

The ending number can be modified to 2、3、4......18、19、20 

Dmeo account：13800000020  password：123456 

### Supported features

-Supports single chat and group chat, and supports sending emoticons, images, voice, video, and file messages
-Single chat supports displaying the status of messages that have been read but not read, and displaying online status
-Group chat creation, deletion, group member management, group announcements, group bans, etc
-Support for top contacts and message privacy;
-Support for setting new message sound reminders and browser notifications
-Support administrator to recall group member messages
-Support group members cannot add friends to each other
-Supports one-on-one audio and video calls (connected to both web and mobile devices, not supported by mini programs)
-Supports online preview of files, images, and most media files
-Support for mobile devices (H5, APP, and mini programs, some functions are not compatible)
-New support for enterprise mode and community mode, with community mode supporting registration and adding friends functions
-The app supports online and offline push of single chat messages (requires self application for unipush service)
-Support simple backend management, including user management, group management, system settings, etc


### Software architecture

Back-end technology stack：`thinkphp6+workerman+redis`

Front-end technology stack：`vue2+Lemon-IMUI+element-UI`


### Installation

> Due to the particularity of instant messaging, it is strictly prohibited to use the source code for Trojan horses, viruses, pornography, gambling, fraud and other industries in violation of local laws and regulations, as well as to engage in criminal activities, such as the use of this software for illegal activities, will be reported to the relevant departments and assist the relevant administrative law enforcement agencies to check!

> The installation program needs to have some experience in PHP and server operation and maintenance, if not, please join the communication group to contact the author, the author provides paid deployment services!

#### Preparatory work
You need to install the running environment first. The BAOTA server is recommended. The LNMP architecture is recommended. The following software needs to be installed:

|  environment | version | remark | Recommended |
| --------- | ---- | ---- | ---|
| linux（centOS）    | >= 7.0 |  The following versions were not tested   | 7.9 |
| nginx    | >= 1.17 |     | latest |
| php | >= 7.1 |  incompatible php8    | 7.3 |
| mysql    | >= 5.7 | Must be 5.7 and above     | 5.7 |
| redis    | >= 5.0 |     | 7.0 |

**Important operation**

1. PHP needs to install an extension : `redis` `fileinfo`

2. PHP needs to undisable the function : `shell_exec` `chown` `exec` `putenv` `proc_open` `pcntl_exec` `pcntl_alarm` `pcntl_fork` `pcntl_waitpid` `pcntl_wait` `pcntl_signal` `pcntl_signal_dispatch`

#### Source code download
Download the full source code and put it on your own server. Take a look at the  [（releases）](https://gitee.com/raingad/im-instant-chat/releases) at the top of the gitee project home page and download the latest release in the distribution.

#### Start installation
1. Create a website by pointing the site's running directory to the 'public' directory in the project root.



2. Enable pseudo-static and set the reverse proxy, the following only shows the pseudo-static and reverse proxy configuration of nginx, Apache is not supported for the time being.


``` 
location ~* (runtime|application)/{
	return 403;
}
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}

#Reverse proxy port 8282, no modification required

location /wss
    {
      proxy_pass http://127.0.0.1:8282;
      proxy_http_version 1.1;
      proxy_set_header Upgrade $http_upgrade;
      proxy_set_header Connection "Upgrade";
      proxy_set_header X-Real-IP $remote_addr;
    }
```

3. If you have a domain name and want to use services such as audio and video calls and voice messaging, you also need to configure a certificate to enable HTTPS. You can use a free 'Let's Encrypt' certificate. If you don't need these services, you can directly use the HTTP protocol, but the functionality will be limited.
   
4. Access your IP or domain name to enter the custom installation wizard.
   

#### If installation fails
1.  Enter  `public\sql\database.sql`  to import the database into your own database.

2.  Enter the project root directory, modify  `example.env` to `.env` , and modify the corresponding database parameters. **Please carefully read the configuration instructions in env**.

> if you want to save chat files to oss, you need to configure them in the background. Do not modify the environment configuration files after configuration.

### Start the message push service
As the chat software requires the use of WebSocket, we need to start Workerman. The system has already built-in the corresponding service, and you can run the service from the backend management homepage ( **accessible from the bottom-left corner after logging in with an administrator account** ). If the backend service fails to start successfully, you need to perform the following debugging steps:

**Reasons for system service startup failure**:

1. If you have started the `php think worker:gateway start` or `php start.php start` commands in the terminal, please run `killall -9 php` in the terminal or restart the server before running again.

2. It could be that PHP is not the default version. Execute `php -v` in the terminal to check if the version number is inconsistent with the selected PHP version when creating the website. If inconsistent, you need to modify the website's version to the default PHP version and install the corresponding dependencies and remove disabled functions for PHP.

3. It could be due to insufficient directory permissions for execution. Reset all directory permissions to `755` for user `www` and try again.

**If the startup fails, you can perform debugging**

4. Enter the project root directory to run `php think worker:gateway start`, or run `php start.php start` to run the message service. Do not use `- d` during testing. Under windows, run the `start_for_ win.bat` file in the root directory directly. Since there are many restrictions on the use of Workerman under Windows, it is recommended to use Linux system in formal environment, while windows system is only recommended for development environment.

5. The message service needs to release port 8282. If you need to modify it, please modify the corresponding parameters in the `WORKER` section of the environment configuration file. For windows users, please modify port 8282 in [`app\worker\start_gateway.php`]. The port number needs to be changed according to the situation.
   
6. The system uses the domain name as the address of the websocket service directly, so it needs to configure the proxy in the nginx of the website and listen to port 8282. The parameters of the proxy configuration have been written in the pseudo-static.

7. For more information about the use of workerman, please visit [workerman official website](https://www.workerman.net/) official website.

8. After the deployment, the password for the administrator account is: `administrator``123456`, and the management entry is located in the lower left corner of the chat interface.

### Install deployment Services

The author provides the installation services of the system, including the back-end and front-end deployment to the online, to ensure the perfect operation of the project, 200 yuan per time, the installation service can provide detailed installation tutorials and interface documents, if necessary, you can contact the author!

### QQ Communication group
If you have any questions, please leave a message or join our QQ group!

It's not easy to create. Click a star.

[QQ Communication group：1031495465](https://qm.qq.com/q/RgHdvLGiMk)

