-- 升级说明
-- 增加群聊禁言到期时间，好友上限，群聊上限。

-- 更新最新的版本需要更新一下配置文件，进入后台重新保存一下基础设置和聊天设置，否则配置信息不完整

ALTER TABLE `yu_group_user` ADD `no_speak_time` INT(11) NOT NULL DEFAULT '0' COMMENT '禁言到期时间' AFTER `is_top`;
ALTER TABLE `yu_user` ADD `friend_limit` INT(11) NOT NULL DEFAULT '0' COMMENT '好友上限' AFTER `setting`;
ALTER TABLE `yu_user` ADD `group_limit` INT(11) NOT NULL DEFAULT '0' COMMENT '群聊上限' AFTER `setting`;