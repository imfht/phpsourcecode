# gitman-web

> 一个轻量级的面向Git的代码自动发布工具.

#hello boy a! w

## 安装指南

#### 签出代码
> git clone https://gitee.com/lianzh/gitman-web.git

#### 配置虚拟主机

> Nginx + Apache环境:

###### [root@VM_114_15_centos ~]# cat /usr/local/nginx/conf/vhost/yourdomain.conf

```
upstream web1.yourdomain{
	server 127.0.0.1:8090; 
}

server {
        listen       80;
        server_name  yourdomain;

        charset utf-8;

        access_log  logs/yourdomain.access.log  main;
		
	location / {
             proxy_redirect off ;
             proxy_set_header Host $host;
             proxy_set_header X-Real-IP $remote_addr;
             proxy_set_header REMOTE-HOST $remote_addr;
             proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
             client_max_body_size 50m;
             client_body_buffer_size 256k;
             proxy_connect_timeout 30;
             proxy_send_timeout 30;
             proxy_read_timeout 60;
             proxy_buffer_size 256k;
             proxy_buffers 4 256k;
             proxy_busy_buffers_size 256k;
             proxy_temp_file_write_size 256k;
             proxy_next_upstream error timeout invalid_header http_500 http_503 http_404;
             proxy_max_temp_file_size 128m;
             proxy_pass    http://web1.yourdomain;
         }

    }

```

###### [root@VM_114_15_centos ~]# cat /usr/local/apache2/conf/extra/httpd-vhosts.conf
```
<VirtualHost *:8090>
    DocumentRoot "/data/www/yourdomain"
    ServerName yourdomain
    <Directory "/data/www/yourdomain">
      Options FollowSymLinks ExecCGI Indexes
      AllowOverride All
      Order allow,deny
      Allow from all
      Require all granted
    </Directory>
</VirtualHost>
```

> 单Apache环境:

```
<VirtualHost *:80>
    DocumentRoot "/data/www/yourdomain"
    ServerName yourdomain
  <Directory "/data/www/yourdomain">
      Options FollowSymLinks ExecCGI Indexes
      AllowOverride All
      Order allow,deny
      Allow from all
      Require all granted
  </Directory>
</VirtualHost>
```

####安装数据库
> 打开项目根目录下的 `db.sql`文件,将其导入进mysql数据库

```
/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.5.53 : Database - gitman_web
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`gitman_web` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `gitman_web`;

/*Table structure for table `gitman_deploy` */

DROP TABLE IF EXISTS `gitman_deploy`;

CREATE TABLE `gitman_deploy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `repository_id` int(10) unsigned NOT NULL COMMENT '仓库标识',
  `name` varchar(80) NOT NULL COMMENT '分支发布名称',
  `webhook_branch_ref` varchar(160) NOT NULL COMMENT 'webhook的钩子引用的分支标识,用于标明提交的时哪个分支',
  `branch_origin` varchar(80) NOT NULL COMMENT '远程分支的名称(用于执行 git pull origin ''分支名称'')',
  `code_dir` varchar(240) NOT NULL COMMENT '本地代码目录',
  `webhook_password` varchar(32) NOT NULL COMMENT 'webhook的钩子调用的安全密码交易',
  `extra_commands` text COMMENT '签出代码之后执行的命令,多条命令使用 ;;; 分隔',
  `xstatus` tinyint(4) NOT NULL DEFAULT '1' COMMENT '启用状态: 1 启用; 0 禁止',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='仓库分支发布';

/*Table structure for table `gitman_hookrecord` */

DROP TABLE IF EXISTS `gitman_hookrecord`;

CREATE TABLE `gitman_hookrecord` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `deploy_id` int(10) unsigned NOT NULL COMMENT '分支部署标识',
  `repository_id` int(10) unsigned NOT NULL COMMENT '仓库标识',
  `do_status` tinyint(8) NOT NULL DEFAULT '0' COMMENT '执行状态: 0 未开始; 1 执行中; 2 执行完成; 3执行失败',
  `do_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行时间',
  `do_msg` varchar(240) NOT NULL DEFAULT '' COMMENT '执行时记录的信息(诸如报错)',
  `commits_info` longtext COMMENT '提交代码数据(json格式)',
  `webhook_name` varchar(120) NOT NULL COMMENT 'webhook_name',
  `webhook_type` tinyint(8) NOT NULL DEFAULT '0' COMMENT 'webhook_type: 1 push; 2 tagPush; 3 issue; 4 pullRequest; 5 comment',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='webhook 信息聚合表';

/*Table structure for table `gitman_repository` */

DROP TABLE IF EXISTS `gitman_repository`;

CREATE TABLE `gitman_repository` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL COMMENT '名称',
  `url` varchar(240) NOT NULL COMMENT 'url:项目的url,唯一',
  `git_http_url` varchar(240) NOT NULL COMMENT 'git_http_url',
  `git_ssh_url` varchar(240) NOT NULL COMMENT 'git_ssh_url',
  `platform` tinyint(8) NOT NULL COMMENT '仓库平台: 1 gitee; 2 github',
  `description` text COMMENT '仓库描述',
  `created_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_index` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='代码仓库表';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

```

#### 修改配置文件
> 将 config.example.php 文件重命名成 config.php, 对其中的参数进行调整

```
<?php

// 配置文件

return array(

	'title'	=> 'gitman-web@lianzh',

	'template_dir' => __DIR__ . '/templates',

	// adminer 相关参数
	'adminer'	=> array(

		// 白名单设置
		'authorize'	=> array(
			'id'	=> 'authorizeId',
			'val'	=> 'gitmyweb001'
		),
		
	),	

	// 数据源配置信息
	'dsn'	=> array(
		'type' => 'mysql',

		'dbpath'  => 'mysql:host=127.0.0.1;port=3306;dbname=gitman_web',
		'login'	=> 'root',
		'password' => 'root',

		'initcmd' => array(
				"SET NAMES 'utf8'",
			),

		'attr'	=> array(
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_PERSISTENT => false,
			),
	),

);

```

> 将数据库的主机,帐号和密码修改与你本机相同,为了安全保证请将`adminer`下的`authorize`参数设置成你自己的,修改完成之后可以通过浏览器来访问 http://yourdomain/adminer.php?authorizeId=gitmyweb001

此时你应该可以来到我们的执行任务列表页面:

![输入图片说明](https://gitee.com/uploads/images/2017/1107/205256_da6cedd9_77511.png "xx.png")

当未配置仓库时,应该看到的是空数据列表页面

#### 创建仓库
> 点击 左上角的 `Repository List` 去到仓库列表页面
![输入图片说明](https://gitee.com/uploads/images/2017/1107/205516_f9b0df10_77511.png "xx01.png")

![输入图片说明](https://gitee.com/uploads/images/2017/1107/205716_c7133eb9_77511.png "xx02.png")

![输入图片说明](https://gitee.com/uploads/images/2017/1107/205935_dbf74b02_77511.png "xx03.png")

![输入图片说明](https://gitee.com/uploads/images/2017/1107/205944_f03c547f_77511.png "xx04.png")

![输入图片说明](https://gitee.com/uploads/images/2017/1107/210217_8f7f1762_77511.png "xx05.png")

> `webhook_branch_ref` 是远程的分支名,`branch_origin`是本地的代码分支名,`code_dir`是程序所在目录,`webhook_password`是webhook的密钥,`extra_commands`是pull完代码之后执行的命令集合,多条命令使用 `;;;` 分隔.

#### 配置码云钩子
使用 http://yourdomain/gitee-push-hooks.php 密码框中输入 上文配置的 `webhook_password`的值.
![输入图片说明](https://gitee.com/uploads/images/2017/1107/210600_a9c812ce_77511.png "xx06.png")

#### 执行agent程序
登录到你的服务器中去到 项目根目录中执行 `php hookrecord-consume.php` 即可

![输入图片说明](https://gitee.com/uploads/images/2017/1107/211824_89f8787b_77511.png "xx08.png")
![输入图片说明](https://gitee.com/uploads/images/2017/1107/210916_5d94ab76_77511.png "xx07.png")

> 此时你每次提交代码到 gitee时 代码就会自动部署进你的服务器中,希望能帮助到诸位使用.
