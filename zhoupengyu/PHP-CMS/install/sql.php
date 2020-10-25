DROP TABLE IF EXISTS `#__admin`;
CREATE TABLE `#__admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `logintime` datetime DEFAULT NULL,
  `loginip` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT '1' COMMENT '1.正常 2.禁用',
  `session_id` varchar(255) DEFAULT NULL,
  `gid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


INSERT INTO `#__admin` VALUES ('1', 'admin', '36e1a5072c78359066ed7715f5ff3da8', null, '2015-09-24 12:58:19', '113.0.56.214', '1', 'e8ce6a80018a77f30724105d7a4a0f1c', '1');


DROP TABLE IF EXISTS `#__admin_group`;
CREATE TABLE `#__admin_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


INSERT INTO `#__admin_group` VALUES ('1', '超级管理员', '最高权限分组');

DROP TABLE IF EXISTS `#__admin_group_auth`;
CREATE TABLE `#__admin_group_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `op` varchar(255) DEFAULT NULL,
  `task` varchar(255) DEFAULT NULL,
  `auth` varchar(255) DEFAULT NULL,
  `gid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2368 DEFAULT CHARSET=utf8;

INSERT INTO `#__admin_group_auth` VALUES ('1', 'core', 'group', 'other', '1');
INSERT INTO `#__admin_group_auth` VALUES ('2', 'core', 'group', 'delete', '1');
INSERT INTO `#__admin_group_auth` VALUES ('3', 'core', 'group', 'insert', '1');
INSERT INTO `#__admin_group_auth` VALUES ('4', 'core', 'group', 'execute', '1');
INSERT INTO `#__admin_group_auth` VALUES ('5', 'core', 'adminList', 'other', '1');
INSERT INTO `#__admin_group_auth` VALUES ('6', 'core', 'adminList', 'delete', '1');
INSERT INTO `#__admin_group_auth` VALUES ('7', 'core', 'adminList', 'insert', '1');
INSERT INTO `#__admin_group_auth` VALUES ('8', 'core', 'adminList', 'execute', '1');
INSERT INTO `#__admin_group_auth` VALUES ('9', 'core', 'menu', 'other', '1');
INSERT INTO `#__admin_group_auth` VALUES ('10', 'core', 'menu', 'delete', '1');
INSERT INTO `#__admin_group_auth` VALUES ('11', 'core', 'menu', 'insert', '1');
INSERT INTO `#__admin_group_auth` VALUES ('12', 'core', 'menu', 'execute', '1');
INSERT INTO `#__admin_group_auth` VALUES ('13', 'core', 'menuManager', 'other', '1');
INSERT INTO `#__admin_group_auth` VALUES ('14', 'core', 'menuManager', 'delete', '1');
INSERT INTO `#__admin_group_auth` VALUES ('15', 'core', 'menuManager', 'insert', '1');
INSERT INTO `#__admin_group_auth` VALUES ('16', 'core', 'menuManager', 'execute', '1');
INSERT INTO `#__admin_group_auth` VALUES ('17', 'core', 'system', 'insert', '1');
INSERT INTO `#__admin_group_auth` VALUES ('18', 'core', 'system', 'execute', '1');
INSERT INTO `#__admin_group_auth` VALUES ('19', 'core', '', 'execute', '1');


DROP TABLE IF EXISTS `#__config`;
CREATE TABLE `#__config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(2000) DEFAULT NULL COMMENT 'seo关键字',
  `description` varchar(2000) DEFAULT NULL COMMENT 'SEO描述',
  `copyright` text,
  `web_open` int(11) DEFAULT NULL COMMENT '1.开启 2关闭',
  `use_seo_url` int(11) DEFAULT '1' COMMENT '1开启 2关闭',
  `page_count_max` int(11) DEFAULT '20' COMMENT '每页数据条数',
  `upfileDirectory` varchar(255) DEFAULT NULL,
  `upfileSize` int(11) DEFAULT '102400' COMMENT '单位kb',
  `url` varchar(255) DEFAULT NULL,
  `start_time_1` datetime DEFAULT NULL,
  `start_time_2` datetime DEFAULT NULL,
  `over_time_1` datetime DEFAULT NULL,
  `over_time_2` datetime DEFAULT NULL,
  `start_book_start` datetime DEFAULT NULL,
  `start_book_over` datetime DEFAULT NULL,
  `map_key` varchar(255) DEFAULT NULL,
  `gps_x` varchar(255) DEFAULT NULL,
  `gps_y` varchar(255) DEFAULT NULL,
  `gps_z` varchar(255) DEFAULT NULL,
  `is_auto_play` int(11) DEFAULT '1' COMMENT '1是 2否',
  `music_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `#__config` VALUES ('1', '61PHP', '61PHP', '61PHP', '&copy; 2016 ', '1', '1', '20', 'uploads/', '102400', '', '2014-08-01 00:00:00', '2014-06-08 00:00:00', '2014-10-31 00:00:00', '2014-06-29 14:00:00', '2014-06-06 00:00:00', '2014-06-28 00:00:00', 'AWQBZ-W7FRW-7SORB-OTUB5-KWCSS-OVBTG', '45.539060', '126.970367', '12', '1', '/uploads/up_files/2014/11/15/20141115110338575.mp3');


DROP TABLE IF EXISTS `#__log`;
CREATE TABLE `#__log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `content` text,
  `addtime` datetime DEFAULT NULL,
  `op` varchar(11) DEFAULT NULL COMMENT '1.清空用户数据 ',
  `task` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__manager_menu`;
CREATE TABLE `#__manager_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `op` varchar(255) DEFAULT NULL,
  `task` varchar(255) DEFAULT NULL,
  `link_type` int(11) DEFAULT '1' COMMENT '1.站内,2站外连接',
  `pid` int(11) DEFAULT '0' COMMENT '板块ID 默认0',
  `execute` int(11) DEFAULT '1' COMMENT '1开启，2没有',
  `select` int(11) DEFAULT '1' COMMENT '1开启，2没有',
  `insert` int(11) DEFAULT '1' COMMENT '1开启，2没有',
  `delete` int(11) DEFAULT '1' COMMENT '1开启，2没有',
  `other` int(11) DEFAULT '1' COMMENT '1开启，2没有',
  `status` int(11) DEFAULT '1' COMMENT '1.开启 2关闭',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;



INSERT INTO `#__manager_menu` (`id`, `title`, `op`, `task`, `link_type`, `pid`, `execute`, `select`, `insert`, `delete`, `other`, `status`, `sort`, `icon`, `url`) VALUES ('1', '系统设定', 'core', 'system', '1', '0', '1', '1', '1', '1', '1', '1', '100', 'icon-cog', 'index.php?m=admin&t=system');
INSERT INTO `#__manager_menu` (`id`, `title`, `op`, `task`, `link_type`, `pid`, `execute`, `select`, `insert`, `delete`, `other`, `status`, `sort`, `icon`, `url`) VALUES ('2', '系统环境设定', 'core', 'system', '1', '1', '1', '2', '1', '2', '2', '1', '99', 'icon-home', 'index.php?m=admin&t=system');
INSERT INTO `#__manager_menu` (`id`, `title`, `op`, `task`, `link_type`, `pid`, `execute`, `select`, `insert`, `delete`, `other`, `status`, `sort`, `icon`, `url`) VALUES ('3', '系统账户', 'core', 'adminList', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'icon-user', 'index.php?m=admin&t=adminList');
INSERT INTO `#__manager_menu` (`id`, `title`, `op`, `task`, `link_type`, `pid`, `execute`, `select`, `insert`, `delete`, `other`, `status`, `sort`, `icon`, `url`) VALUES ('4', '后台菜单', 'core', 'menuManager', '1', '1', '1', '1', '1', '1', '1', '1', '98', 'icon-th-list', 'index.php?m=admin&t=menuManager');
INSERT INTO `#__manager_menu` (`id`, `title`, `op`, `task`, `link_type`, `pid`, `execute`, `select`, `insert`, `delete`, `other`, `status`, `sort`, `icon`, `url`) VALUES ('5', '管理员群组', 'core', 'group', '1', '1', '1', '1', '1', '1', '1', '1', '0', 'icon-group', 'index.php?m=admin&o=core&t=group');


DROP TABLE IF EXISTS `#__page_class`;
CREATE TABLE `#__page_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `status` int(11) DEFAULT '1' COMMENT '1.开启 2.关闭',
  `pid` int(11) DEFAULT '0' COMMENT '父ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


INSERT INTO `#__page_class` VALUES ('1', '默认分类', '0', '1', '0');


DROP TABLE IF EXISTS `#__province`;
CREATE TABLE `#__province` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `status` int(3) NOT NULL DEFAULT '2',
  `sort` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=144 DEFAULT CHARSET=utf8;


INSERT INTO `#__province` VALUES ('1', '北京', '1', '0');
INSERT INTO `#__province` VALUES ('2', '天津', '1', '0');
INSERT INTO `#__province` VALUES ('3', '河北', '1', '0');
INSERT INTO `#__province` VALUES ('4', '山西', '1', '0');
INSERT INTO `#__province` VALUES ('5', '内蒙古', '1', '0');
INSERT INTO `#__province` VALUES ('6', '辽宁', '1', '0');
INSERT INTO `#__province` VALUES ('7', '吉林', '1', '0');
INSERT INTO `#__province` VALUES ('8', '黑龙江', '1', '0');
INSERT INTO `#__province` VALUES ('9', '上海', '1', '0');
INSERT INTO `#__province` VALUES ('10', '江苏', '1', '0');
INSERT INTO `#__province` VALUES ('11', '浙江', '1', '0');
INSERT INTO `#__province` VALUES ('12', '安徽', '1', '0');
INSERT INTO `#__province` VALUES ('13', '福建', '1', '0');
INSERT INTO `#__province` VALUES ('14', '江西', '1', '0');
INSERT INTO `#__province` VALUES ('15', '山东', '1', '0');
INSERT INTO `#__province` VALUES ('16', '河南', '1', '0');
INSERT INTO `#__province` VALUES ('17', '湖北', '1', '0');
INSERT INTO `#__province` VALUES ('18', '湖南', '1', '0');
INSERT INTO `#__province` VALUES ('19', '广东', '1', '0');
INSERT INTO `#__province` VALUES ('20', '广西', '1', '0');
INSERT INTO `#__province` VALUES ('21', '海南', '1', '0');
INSERT INTO `#__province` VALUES ('22', '重庆', '1', '0');
INSERT INTO `#__province` VALUES ('23', '四川', '1', '0');
INSERT INTO `#__province` VALUES ('24', '贵州', '1', '0');
INSERT INTO `#__province` VALUES ('25', '云南', '1', '0');
INSERT INTO `#__province` VALUES ('26', '西藏', '1', '0');
INSERT INTO `#__province` VALUES ('27', '陕西', '1', '0');
INSERT INTO `#__province` VALUES ('28', '甘肃', '1', '0');
INSERT INTO `#__province` VALUES ('29', '青海', '1', '0');
INSERT INTO `#__province` VALUES ('30', '宁夏', '1', '0');
INSERT INTO `#__province` VALUES ('31', '新疆', '1', '0');
INSERT INTO `#__province` VALUES ('32', '其它', '1', '0');
