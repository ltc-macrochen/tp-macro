CREATE DATABASE `db_ccb_nuoxin` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
USE `db_ccb_nuoxin`;
set names utf8;

create table if not exists `t_user` (
  `id` int(11) unsigned not null AUTO_INCREMENT,
  `user_name` varchar(40) not null default '' comment '登录名',
  `user_role` int(11) unsigned not null default '0' comment '用户角色',
  `user_password` varchar(40) not null default '' comment '用户密码',
  `user_head` varchar(255) not null default '' comment '用户头像',
  `nickname` varchar(40) not null default '' comment '昵称',
  `email` varchar(255) not null default '' comment '邮箱',
  `mobile` varchar(11) not null default '' comment '手机号',
  `gender` tinyint not null default 0 comment '性别，0未定义，1男，2女',
  `status` int(11) unsigned not null default '0' comment '用户状态',
  `last_login_at` int default 0 comment '最近登录时间',
  primary key (`id`),
  unique key `user_name_unique` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment='用户信息';

DROP TABLE IF EXISTS `t_girls`;
CREATE TABLE `t_girls` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL COMMENT '姓名',
  `head` VARCHAR(255) NOT NULL COMMENT '头像',
  `video` VARCHAR(255) NOT NULL COMMENT '视频介绍',
  `area` VARCHAR(32) NOT NULL COMMENT '赛区',
  `vote_count` INT UNSIGNED DEFAULT 0 COMMENT '票数',
  `status` TINYINT DEFAULT 0 COMMENT '状态',
  `created_at` INT DEFAULT 0 COMMENT '创建时间',
  `updated_at` INT DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_status_votecount` (`status`, `vote_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `t_vote_history`;
CREATE TABLE `t_vote_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `girl_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `count` INT UNSIGNED DEFAULT 1 COMMENT '票数',
  `ip` VARCHAR(16) NOT NULL COMMENT 'IP',
  `created_at` INT DEFAULT 0 COMMENT '投票时间',
  PRIMARY KEY (`id`),
  KEY `fk_girl_id` (`girl_id`),
  CONSTRAINT FOREIGN KEY (`girl_id`) REFERENCES `t_girls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;