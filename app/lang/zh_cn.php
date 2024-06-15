<?php
return [
    'system'=>[
        'success'=>'操作成功',
        'fail'=>'操作失败',
        'error'=>'系统错误',
        'forbidden'=>"禁止访问",
        'sendOK'=>"发送成功",
        'sendFail'=>"发送失败",
        'delOk'=>"删除成功",
        'settingOk'=>"设置成功",
        'notNull'=>"不能为空",
        'editOk'=>'修改成功',
        'editFail'=>'修改失败',
        'addOk'=>'添加成功',
        'addFail'=>'添加成功',
        'joinOk'=>'加入成功',
        'notAuth'=>"您没有操作权限！",
        'demoMode'=>"演示模式不支持修改",
        'parameterError'=>"参数错误",
        'longTime'=>'请求超时',
        'apiClose'=>"接口已关闭",
        'appIdError'=>'appId错误',
        'signError'=>'签名错误',
    ],
    'messageType'=>[
        'other'=>"[暂不支持的消息类型]",
        'image'=>'[图片]',
        'voice'=>'[语音]',
        'video'=>'[视频]',
        'file'=>'[文件]',
        'webrtcAudio'=>'[正在请求与您语音通话]',
        'webrtcVideo'=>'[正在请求与您视频通话]',
    ],
    'friend'=>[
        'notAddOwn'=>"不能添加自己为好友",
        'already'=>"你们已经是好友了",
        'repeatApply'=>"你已经申请过了，请等待对方同意",
        'new'=>"新朋友",
        "apply"=>"添加您为好友",
        'notApply'=>"申请不存在",
        'not'=>"好友不存在",
    ],
    'group'=>[
        'name'=>"群聊",
        'notAuth'=>'你没有操作权限，只有群主和群管理员才可以修改！',
        'userLimit'=>'人数不能超过{:userMax}人!',
        'invite'=>"{:username}邀请你加入群聊",
        'add'=>"{:username}创建了群聊",
        'atLeast'=>"请至少选择两人！",
        'alreadyJoin'=>'您已经加入该群！',
        'exist'=>"群聊不存在",
        'notice'=>"群公告",
        'all'=>"所有人",
    ],
    'user'=>[
        'exist'=>"用户不存在",
        'codeErr'=>'验证码不正确！',
        'newCodeErr'=>'新验证码不正确！',
        'passErr'=>"原密码不正确！",
        'already'=>"账户已存在",
        'registerOk'=>"注册成功",
        'loginOk'=>"登陆成功",
        'tokenFailure'=>"TOKEN已失效！",
        'forbid'=>'您的账号已被禁用',
        'passError'=>'密码错误',
        'logoutOk'=>'退出成功！',
        'closeRegister'=>'当前系统已关闭注册功能！',
        'inviteCode'=>'邀请码已失效！',
        'accountVerify'=>'账户必须为手机号或者邮箱',
        'waitMinute'=>"请一分钟后再试！",
        "loginAccount"=>"登录账户",
        "registerAccount"=>"注册账户",
        "editPass"=>"修改密码",
        "editAccount"=>"修改账户",
        'loginError' => '登陆信息有误 请重新登录',
        'mustToken' => '请先登陆系统',
        'blacklist' => '登陆已失效 请重新登陆',
        'expired' => '登陆已过期 请重新登陆'
    ],
    'im'=>[
        'forbidChat'=>"目前禁止用户私聊！",
        'notFriend'=>"您不在TA的好友列表，不能发消息！",
        'friendNot'=>"TA还不是您的好友，不能发消息！",
        'forwardLimit'=>"请选择转发的用户或者数量不操作{:count}个！",
        'exist'=>"消息不存在",
        'forwardRule'=>"由于规则限制，转发失败{:count}条！",
        'forwardOk'=>'转发成功',
        'you'=>'你',
        'other'=>'对方',
        'redoLimitTime'=>"超过{:time}分钟不能撤回！",
        'redo'=>"撤回了一条消息",
        'manageRedo'=>'被(管理员)撤回了一条消息'
    ],
    'webRtc'=>[
        'cancel'=>'已取消通话',
        'refuse'=>'已拒绝',
        'notConnected'=>'未接通',
        'duration'=>'通话时长：{:time}',
        'busy'=>'忙线中',
        'other'=>'其他端已操作',
        'video'=>'视频通话',
        'audio'=>'语音通话',
        'answer'=>'接听通话请求',
        'exchange'=>'数据交换中',
        'fail'=>'通话失败',
    ],
    'email'=>[
        'input'=>'请输入正确的邮箱',
        'testTitle'=>"测试邮件",
        'testContent'=>'这是一封测试邮件，当您收到之后表明您的所有配置都是正确的！',
    ],
    'task'=>[
        'schedule' => '计划任务',
        'queue' => '消息队列',
        'worker' => '消息推送',
        'clearStd' => '清理日志',
        'null'=>"未知任务",
        'winRun'=>"windows启动请运行根目录下的：start_for_win.bat",
        'alreadyRun'=>"进程已启动",
        'startOk'=>"启动成功",
        'startFail'=>"启动失败",
        'notRun'=>"进程未启动",
        'logExist'=>"日志不存在",
    ],
    'file'=>[
        'preview'=>"预览文件",
        'browserDown'=>"请使用浏览器下载",
        'exist'=>"预览文件",
        'uploadLimit'=>"文件大小不能超过{:size}MB",
        'typeNotSupport'=>"文件格式不支持",
        'uploadOk'=>"上传成功"
    ],
    'scan'=>[
        'failure'=>'二维码已失效'
    ]
];