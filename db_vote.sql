CREATE DATABASE `db_vote` DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
USE `db_vote`;
set names utf8;

DROP TABLE IF EXISTS `t_girls`;
CREATE TABLE `t_girls` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(32) NOT NULL COMMENT '姓名',
  `head` VARCHAR(255) NOT NULL COMMENT '头像',
  `votecount` INT UNSIGNED DEFAULT 0 COMMENT '票数',
  `status` TINYINT DEFAULT 0 COMMENT '状态',
  `created_at` INT DEFAULT 0 COMMENT '创建时间',
  `updated_at` INT DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY ('id'),
  INDEX `index_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `t_vote_history`;
CREATE TABLE `t_vote_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `girl_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
  `ip` VARCHAR(16) NOT NULL COMMENT 'IP',
  `created_at` INT DEFAULT 0 COMMENT '投票时间',
  PRIMARY KEY ('id'),
  KEY `fk_girl_id` (`girl_id`),
  CONSTRAINT FOREIGN KEY (`girl_id`) REFERENCES `t_girls` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;