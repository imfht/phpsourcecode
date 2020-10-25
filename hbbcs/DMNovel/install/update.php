ALTER TABLE `story` ADD `score` SMALLINT NULL AFTER `click`;

RENAME TABLE user TO users;
ALTER TABLE `users` ADD `level` TINYINT NOT NULL ;
ALTER TABLE `users` CHANGE `avater` `avatar` CHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

CREATE TABLE IF NOT EXISTS `sessions` (
`id` varchar(40) NOT NULL,
`ip_address` varchar(45) NOT NULL,
`timestamp` int(10) unsigned NOT NULL DEFAULT '0',
`data` blob NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE sessions ADD PRIMARY KEY (id);

//2017-01-09

CREATE TABLE IF NOT EXISTS `bookmark` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11) NOT NULL,
`story_id` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `story` ADD `approve` TINYINT(1) NOT NULL DEFAULT '0' ;

UPDATE `story` SET `approve`=1;

//2017-01-10
CREATE TABLE captcha (
captcha_id bigint(13) unsigned NOT NULL auto_increment,
captcha_time int(10) unsigned NOT NULL,
ip_address varchar(45) NOT NULL,
word varchar(20) NOT NULL,
PRIMARY KEY `captcha_id` (`captcha_id`),
KEY `word` (`word`)
);

ALTER TABLE `story` CHANGE `click` `vote` SMALLINT NOT NULL;

ALTER TABLE `users` CHANGE `bookmark` `vote` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

//2017-01-11
ALTER TABLE `story` ADD `average` DECIMAL( 2, 1 ) NOT NULL DEFAULT '0' AFTER `score`, ADD `mark` MEDIUMINT NOT NULL DEFAULT '0' AFTER `average`;

//2017-01-13
ALTER TABLE `users` ADD `mail` VARCHAR(200) NULL DEFAULT NULL AFTER `password`;
ALTER TABLE `users` ADD `notify` TINYINT(1) NOT NULL DEFAULT '0' ;