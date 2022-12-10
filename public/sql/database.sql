--
-- Database: `im`
--

-- --------------------------------------------------------

--
-- 表的结构 `yu_file`
--

CREATE TABLE IF NOT EXISTS `yu_file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `cate` tinyint(1) NOT NULL DEFAULT '9' COMMENT '文件分类',
  `file_type` varchar(128) DEFAULT NULL COMMENT '文件类型',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父Id',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `src` varchar(255) DEFAULT NULL COMMENT '链接',
  `size` int(11) DEFAULT '0' COMMENT '大小',
  `ext` varchar(16) DEFAULT NULL COMMENT '文件后缀',
  `md5` varchar(32) DEFAULT NULL COMMENT 'md5',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='文件库';

-- --------------------------------------------------------

--
-- 表的结构 `yu_friend`
--

CREATE TABLE IF NOT EXISTS `yu_friend` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `friend_user_id` varchar(32) DEFAULT NULL COMMENT '好友ID',
  `is_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为群聊',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `is_notice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否消息提醒',
  `create_user` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `satus` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`friend_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='联系人置顶表';

-- --------------------------------------------------------

--
-- 表的结构 `yu_group`
--

CREATE TABLE IF NOT EXISTS `yu_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '团队名称',
  `name_py` varchar(64) DEFAULT NULL COMMENT '团队的拼音',
  `level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '等级',
  `create_user` int(11) NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `owner_id` int(11) NOT NULL DEFAULT '0' COMMENT '拥有者',
  `notice` text COMMENT '公告',
  `setting` text COMMENT '设置',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `yu_group_user`
--

CREATE TABLE IF NOT EXISTS `yu_group_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT 't团队Id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户Id',
  `role` tinyint(1) NOT NULL DEFAULT '2' COMMENT '角色 1拥有者，2管理员，3成员',
  `invite_id` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `unread` int(11) NOT NULL DEFAULT '0' COMMENT '群未读消息',
  `is_notice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1是否提醒',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0 ，未同意邀请，1，同意',
  `isdelete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `yu_message`
--

CREATE TABLE IF NOT EXISTS `yu_message` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(36) DEFAULT NULL COMMENT '消息id',
  `from_user` int(11) NOT NULL DEFAULT '0' COMMENT '发送者',
  `to_user` int(11) NOT NULL DEFAULT '0' COMMENT '接受收者',
  `content` text COMMENT '消息内容，如果为文件或图片就是url',
  `chat_identify` varchar(64) DEFAULT NULL COMMENT '标识 ：a与b聊天，b与a聊天。记录 a-b',
  `type` varchar(32) NOT NULL DEFAULT 'text' COMMENT '消息类型：text、file、image...',
  `is_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '群聊消息',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否阅读',
  `is_last` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是最后一条消息',
  `create_time` int(13) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `is_undo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否撤回',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(256) DEFAULT NULL COMMENT '文件名称',
  `extends` json DEFAULT NULL COMMENT '消息扩展内容',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `del_user` text COMMENT '已删除成员',
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `yu_user`
--

CREATE TABLE IF NOT EXISTS `yu_user` (
  `user_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account` char(32) NOT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `password` char(32) NOT NULL,
  `salt` varchar(4) DEFAULT NULL COMMENT '加密盐',
  `avatar` varchar(128) DEFAULT NULL COMMENT '头像',
  `email` varchar(50) DEFAULT NULL COMMENT '电子邮箱',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `name_py` varchar(64) DEFAULT NULL COMMENT '名字的拼音',
  `setting` text COMMENT '用户设置',
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(11) UNSIGNED DEFAULT NULL,
  `login_count` mediumint(8) UNSIGNED DEFAULT '0' COMMENT '登录次数',
  `last_login_time` int(11) UNSIGNED DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` char(15) DEFAULT NULL COMMENT '最后登录Ip\n',
  `isdelete` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(1) UNSIGNED DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `account` (`account`) USING BTREE,
  KEY `accountpassword` (`account`,`password`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `yu_user`
--

INSERT INTO `yu_user` (`account`, `realname`, `password`, `salt`, `avatar`, `email`, `remark`, `name_py`, `setting`, `create_time`, `update_time`, `login_count`, `last_login_time`, `last_login_ip`, `isdelete`, `status`) VALUES
( 'administrator', '管理员', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad1.png?apikey=zdvXV3W4MjwhP9', 'service@kaishanlaw.com', '我是超级管理员，不要来惹我撒', 'guanliyuan', '{\"theme\":\"blue\",\"hideMessageName\":\"true\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"true\",\"sendKey\":\"1\",\"is_voice\":\"false\",\"isVoice\":\"true\"}', 1222907803, 1451033528, 3024, 1617699672, '61.157.13.16', 0, 1),
('13800000002', '熊大', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad2.png?apikey=zdvXV3W4MjwhP9', 'xiekunyu@kaishanlaw.com', '123', 'xiongda', '{\"theme\":\"blue\",\"hideMessageName\":\"true\",\"hideMessageTime\":\"true\",\"avatarCricle\":\"true\",\"sendKey\":\"2\",\"is_voice\":\"false\",\"isVoice\":\"true\"}', 1555341865, 1558019786, 4, 1558020894, '223.87.209.87', 0, 1),
('13800000003', '熊二', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad3.png?apikey=zdvXV3W4MjwhP9', 'xiekunlin@163.com', '', 'xionger', '{\"theme\":\"default\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"true\",\"sendKey\":\"1\",\"is_voice\":\"false\",\"isVoice\":\"false\"}', 1557933999, 1604587104, 0, 0, NULL, 0, 1),
('13800000004', '喜洋洋', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad4.png?apikey=zdvXV3W4MjwhP9', 'wuhong@qq.com', '', 'xiyangyang', '{\"theme\":\"blue\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"false\",\"sendKey\":\"1\",\"isVoice\":\"true\",\"isShowGameTip\":\"false\",\"isShowSet\":\"false\"}', 1604587165, 1604587250, 0, 0, NULL, 0, 1),
('13800000005', '灰太狼', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad5.png?apikey=zdvXV3W4MjwhP9', 'zengyong@qq.com', '', 'huitailang', '{\"theme\":\"default\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"false\",\"sendKey\":\"1\",\"isVoice\":\"true\"}', 1604587246, 1604587246, 0, 0, NULL, 0, 1),
('13800000006', '奥特曼', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad6.png?apikey=zdvXV3W4MjwhP9', 'hangjiwei@qq.com', '', 'aoteman', '{\"theme\":\"blue\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"true\",\"sendKey\":\"2\",\"isVoice\":\"true\",\"isShowGameTip\":\"false\",\"isShowSet\":\"false\"}', 1604587295, 1604587295, 0, 0, NULL, 0, 1),
('13800000007', '孙悟空', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad7.png?apikey=zdvXV3W4MjwhP9', 'lijiaxin@qq.com', '', 'sunwukong', '{\"theme\":\"blue\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"true\",\"avatarCricle\":\"true\",\"sendKey\":\"2\",\"isVoice\":\"true\",\"isShowGameTip\":\"true\",\"isShowSet\":\"true\"}', 1604587347, 1604587347, 0, 0, NULL, 0, 1),
('13800000008', '猪八戒', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad8.png?apikey=zdvXV3W4MjwhP9', 'liaomingheng@qq.com', '', 'zhubajie', '{\"theme\":\"blue\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"true\",\"sendKey\":\"2\",\"is_voice\":\"false\",\"isVoice\":\"true\"}', 1604587378, 1604587378, 0, 0, NULL, 0, 1),
('13800000009', '唐三藏', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad9.png?apikey=zdvXV3W4MjwhP9', 'lantian@qq.com', '', 'tangsanzang', '{\"theme\":\"blue\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"true\",\"sendKey\":\"2\",\"isVoice\":\"true\",\"isShowGameTip\":\"false\",\"isShowSet\":\"false\"}', 1604587409, 1604587409, 0, 0, NULL, 0, 1),
('13800000010', '沙悟净', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', 'https://api.multiavatar.com/raingad10.png?apikey=zdvXV3W4MjwhP9', 'lantian@qq.com', '', 'tangsanzang', '{\"theme\":\"default\",\"hideMessageName\":\"false\",\"hideMessageTime\":\"false\",\"avatarCricle\":\"true\",\"sendKey\":\"1\",\"isVoice\":\"true\"}', 1604587409, 1604587409, 0, 0, NULL, 0, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
