

--
-- Database: `imts`
--

-- --------------------------------------------------------

--
-- 表的结构 `yu_config`
--

CREATE TABLE IF NOT EXISTS `yu_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` json DEFAULT NULL,
  `create_user` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='配置表';

--
-- 转存表中的数据 `yu_config`
--

INSERT INTO `yu_config` (`id`, `name`, `value`, `create_user`, `update_time`, `create_time`, `remark`, `status`) VALUES
(1, 'sysInfo', '{\"logo\": \"https://im.file.raingad.com/logo/logo.png\", \"name\": \"raingad-IM\", \"state\": \"1\", \"regtype\": \"2\", \"runMode\": \"1\", \"closeTips\": \"系统升级维护中，请稍候再试！\", \"description\": \"一款基于vue2.0的即时通信系统\"}', 0, 1688462862, 1688462862, NULL, 1),
(2, 'chatInfo', '{\"stun\": \"\", \"online\": \"1\", \"webrtc\": \"1\", \"msgClear\": \"0\", \"stunPass\": \"\", \"stunUser\": \"\", \"groupChat\": \"1\", \"simpleChat\": \"1\", \"msgClearDay\": \"0\", \"groupUserMax\": \"0\"}', 0, 1688463300, 1688463300, NULL, 1),
(3, 'smtp', '{\"addr\": \"\", \"host\": \"\", \"pass\": \"\", \"port\": \"465\", \"sign\": \"\", \"security\": \"ssl\"}', 0, 1688464072, 1688464072, NULL, 1),
(4, 'fileUpload', '{\"disk\": \"local\", \"size\": \"50\", \"qiniu\": {\"url\": \"\", \"bucket\": \"\", \"accessKey\": \"\", \"secretKey\": \"\"}, \"aliyun\": {\"url\": \"\", \"bucket\": \"\", \"accessId\": \"\", \"endpoint\": \"\", \"accessSecret\": \"\"}, \"qcloud\": {\"cdn\": \"\", \"appId\": \"\", \"bucket\": \"\", \"region\": \"\", \"secretId\": \"\", \"secretKey\": \"\"}, \"fileExt\": [\"jpg\", \"jpeg\", \"png\", \"bmp\", \"gif\", \"pdf\", \"mp3\", \"wav\", \"wmv\", \"amr\", \"mp4\", \"3gp\", \"avi\", \"m2v\", \"mkv\", \"mov\", \"webp\", \"ppt\", \"pptx\", \"doc\", \"docx\", \"xls\", \"xlsx\", \"txt\", \"md\"], \"preview\": \"\"}', 0, 1688464130, 1688464130, NULL, 1);

-- --------------------------------------------------------

--
-- 表的结构 `yu_file`
--

CREATE TABLE IF NOT EXISTS `yu_file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `cate` tinyint(1) NOT NULL DEFAULT '9' COMMENT '文件分类',
  `file_type` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文件类型',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父Id',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `src` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '链接',
  `size` int(11) DEFAULT '0' COMMENT '大小',
  `ext` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文件后缀',
  `md5` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'md5',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `is_lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否锁定',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='文件库';

-- --------------------------------------------------------

--
-- 表的结构 `yu_friend`
--

CREATE TABLE IF NOT EXISTS `yu_friend` (
  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
  `friend_user_id` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '好友ID',
  `nickname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '好友备注',
  `is_invite` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为邀请方',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `is_notice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否消息提醒',
  `create_user` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '申请备注',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='联系人置顶表';

-- --------------------------------------------------------

--
-- 表的结构 `yu_group`
--

CREATE TABLE IF NOT EXISTS `yu_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '团队名称',
  `name_py` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '团队的拼音',
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '群聊头像',
  `level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '等级',
  `create_user` int(11) NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `owner_id` int(11) NOT NULL DEFAULT '0' COMMENT '拥有者',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否公开',
  `notice` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '公告',
  `setting` mediumtext COLLATE utf8mb4_unicode_ci COMMENT '设置',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `yu_group_user`
--

CREATE TABLE IF NOT EXISTS `yu_group_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '团队Id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户Id',
  `role` tinyint(1) NOT NULL DEFAULT '2' COMMENT '角色 1拥有者，2管理员，3成员',
  `invite_id` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `unread` int(11) NOT NULL DEFAULT '0' COMMENT '群未读消息',
  `is_notice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1是否提醒',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0 ，未同意邀请，1，同意',
  `delete_time` int(11) NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `yu_message`
--

CREATE TABLE IF NOT EXISTS `yu_message` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '消息id',
  `from_user` int(11) NOT NULL DEFAULT '0' COMMENT '发送者',
  `to_user` int(11) NOT NULL DEFAULT '0' COMMENT '接受收者',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '消息内容，如果为文件或图片就是url',
  `chat_identify` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '标识 ：a与b聊天，b与a聊天。记录 a-b',
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text' COMMENT '消息类型：text、file、image...',
  `is_group` tinyint(1) NOT NULL DEFAULT '0' COMMENT '群聊消息',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否阅读',
  `is_last` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是最后一条消息',
  `create_time` int(13) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `is_undo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否撤回',
  `at` text COLLATE utf8mb4_unicode_ci COMMENT '提及某人',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '引用的消息ID',
  `file_id` int(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `file_cate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '文件类型',
  `file_size` int(11) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '文件名称',
  `extends` json DEFAULT NULL COMMENT '消息扩展内容',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `del_user` text COLLATE utf8mb4_unicode_ci COMMENT '已删除成员',
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `yu_user`
--

CREATE TABLE IF NOT EXISTS `yu_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `account` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `realname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salt` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '加密盐',
  `avatar` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '电子邮箱',
  `sex` tinyint(1) NOT NULL DEFAULT '2' COMMENT '性别，0女，1男，2未知',
  `role` tinyint(1) NOT NULL DEFAULT '0' COMMENT '角色，0无角色，1超管，2普管',
  `motto` text COLLATE utf8mb4_unicode_ci COMMENT '个性签名',
  `remark` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `name_py` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名字的拼音',
  `setting` json DEFAULT NULL COMMENT '用户设置',
  `create_time` int(11) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` int(11) UNSIGNED DEFAULT NULL,
  `login_count` mediumint(8) UNSIGNED DEFAULT '0' COMMENT '登录次数',
  `is_auth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否认证',
  `last_login_time` int(11) UNSIGNED DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` char(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '最后登录Ip\n',
  `register_ip` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '注册IP',
  `delete_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(1) UNSIGNED DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `account` (`account`) USING BTREE,
  KEY `accountpassword` (`account`,`password`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `yu_user`
--

INSERT INTO `yu_user` (`user_id`, `account`, `realname`, `password`, `salt`, `avatar`, `email`, `sex`, `role`, `motto`, `remark`, `name_py`, `setting`, `create_time`, `update_time`, `login_count`, `is_auth`, `last_login_time`, `last_login_ip`, `register_ip`, `delete_time`, `status`) VALUES
(1, 'administrator', '管理员', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'service@kaishanlaw.com', 2, 1, NULL, '', 'guanliyuan', NULL, 1222907803, 1451033528, 4, 0, 1693189512, '218.89.238.248', NULL, 0, 1),
(2, '13800000002', '熊大', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'xiongda@kaishanlaw.com', 2, 0, NULL, '', 'xiongda', NULL, 1555341865, 1558019786, 1, 0, 0, NULL, NULL, 0, 1),
(3, '13800000003', '熊二', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'xionger@163.com', 2, 0, NULL, '', 'xionger', NULL, 1557933999, 1604587104, 0, 0, 0, NULL, NULL, 0, 1),
(4, '13800000004', '喜洋洋', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'xiyangyang@qq.com', 2, 0, NULL, '', 'xiyangyang', NULL, 1604587165, 1604587250, 0, 0, 0, NULL, NULL, 0, 1),
(5, '13800000005', '灰太狼', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'huitailang@qq.com', 2, 0, NULL, '', 'huitailang', NULL, 1604587246, 1604587246, 0, 0, 0, NULL, NULL, 0, 1),
(6, '13800000006', '奥特曼', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'aoteman@qq.com', 2, 0, NULL, '', 'aoteman', NULL, 1604587295, 1604587295, 0, 0, 0, NULL, NULL, 0, 1),
(7, '13800000007', '孙悟空', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'sunwukong@qq.com', 2, 0, NULL, '', 'sunwukong', NULL, 1604587347, 1604587347, 0, 0, 0, NULL, NULL, 0, 1),
(8, '13800000008', '猪八戒', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'zhubajie@qq.com', 2, 0, NULL, '', 'zhubajie', NULL, 1604587378, 1604587378, 0, 0, 0, NULL, NULL, 0, 1),
(9, '13800000009', '唐三藏', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'tangsanzang@qq.com', 2, 0, NULL, '', 'tangsanzang', NULL, 1604587409, 1604587409, 0, 0, 0, NULL, NULL, 0, 1),
(10, '13800000010', '沙悟净', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'lantian@qq.com', 2, 0, NULL, '', 'shawujing', NULL, 1604587409, 1604587409, 0, 0, 0, NULL, NULL, 0, 1),
(11, '13800000011', '刘备', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'liubei@kaishanlaw.com', 2, 0, NULL, '', 'liubei', NULL, 1555341865, 1558019786, 4, 0, 0, NULL, NULL, 0, 1),
(12, '13800000012', '关羽', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'guanyu@163.com', 2, 0, NULL, '', 'guanyu', NULL, 1557933999, 1604587104, 0, 0, 0, NULL, NULL, 0, 1),
(13, '13800000013', '张飞', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'zhangfei@qq.com', 2, 0, NULL, '', 'zhangfei', NULL, 1604587165, 1604587250, 0, 0, 0, NULL, NULL, 0, 1),
(14, '13800000014', '赵云', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'zhaoyun@qq.com', 2, 0, NULL, '', 'zhaoyun', NULL, 1604587246, 1604587246, 0, 0, 0, NULL, NULL, 0, 1),
(15, '13800000015', '曹操', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'caocao@qq.com', 2, 0, NULL, '', 'caocao', NULL, 1604587295, 1604587295, 0, 0, 0, NULL, NULL, 0, 1),
(16, '13800000016', '司马懿', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'simayi@qq.com', 2, 0, NULL, '', 'simayi', NULL, 1604587347, 1604587347, 0, 0, 0, NULL, NULL, 0, 1),
(17, '13800000017', '孙权', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'sunquan@qq.com', 2, 0, NULL, '', 'sunquan', NULL, 1604587378, 1604587378, 0, 0, 0, NULL, NULL, 0, 1),
(18, '13800000018', '周瑜', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'zhouyu@qq.com', 2, 0, NULL, '', 'zhouyu', NULL, 1604587409, 1604587409, 0, 0, 0, NULL, NULL, 0, 1),
(19, '13800000019', '诸葛亮', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'zhugeliang@qq.com', 2, 0, NULL, '', 'zhugeliang', NULL, 1604587378, 1604587378, 0, 0, 0, NULL, NULL, 0, 1),
(20, '13800000020', '吕布', '2cb4ecb7fd5295685e275edc7d44e02e', 'srww', NULL, 'lvbu@qq.com', 2, 0, NULL, '', 'lvbu', NULL, 1604587409, 1604587409, 0, 0, 0, NULL, NULL, 0, 1);
COMMIT;

