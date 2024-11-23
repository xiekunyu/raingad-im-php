-- 升级说明
-- 增加群聊禁言到期时间，好友上限，群聊上限。

-- 更新最新的版本需要更新一下配置文件，进入后台重新保存一下基础设置和聊天设置，否则配置信息不完整

CREATE TABLE `yu_emoji` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id，0为系统',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `src` varchar(255) DEFAULT NULL COMMENT '链接',
  `file_id` INT(11) NOT NULL DEFAULT '0' COMMENT '文件id',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表情表';

--
-- 表的索引 `yu_emoji`
--
ALTER TABLE `yu_emoji`
  ADD PRIMARY KEY (`id`);
