源码地址:
https://centos.googlecode.com/files/pure-ftpd-1.0.36.tar.gz

安装步骤:

准备工作，安装mysqlclient：
ubuntu：apt-get install libpam0g-dev  libmysqlclient-dev
centos：yum install mysql

编译参数：

./configure --prefix=/server/pureftpd --with-mysql  --with-shadow   --with-welcomemsg --with-uploadscript --with-cookie --with-virtualchroot  --with-virtualhosts  --with-diraliases --with-quotas  --with-puredb --with-sysquotas --with-ratios  --with-ftpwho  --with-throttling  --with-language=simplified-chinese --with-rfc2640

如果加上--with-pam ，需要安装pam，ubuntu是apt-get install libpam0g，
centos是yum install pam-dev
不建议--with-pam

mysql配置：

把压缩包里面的etc目录，pure-config.pl，ftpd，复制到安装目录/server/pureftpd
1.修改ftpd，设置里面的路径
pure_config_pl=/server/pureftpd/pure-config.pl
pure_ftpd_conf=/server/pureftpd/etc/pure-ftpd.conf

2.修改pure-config.pl，设置里面的路径
/server/pureftpd/sbin/pure-ftpd

3.编辑etc/mysql.conf,修改为自己的数据库信息
//主机地址，当没有使用sock时
MYSQLServer     127.0.0.1
//端口
MYSQLPort       3306
//mysql socket，MYSQLServer和MYSQLPort 与 MYSQLSocket 是而选一的。
#MYSQLSocket     /tmp/mysql.sock
//数据库用户名
MYSQLUser      dba
//数据库密码
MYSQLPassword   admin
//数据库名
MYSQLDatabase  ftpusers

4.
创建数据库:
CREATE DATABASE  `ftpusers` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

在上面的数据库中执行下面的sql，建立一个users表，里面存放的就是ftp用户

CREATE TABLE `users` (
  `User` varchar(16) NOT NULL DEFAULT '' COMMENT 'ftp用户名,最多16位',
  `Password` varchar(32) NOT NULL DEFAULT '' COMMENT 'ftp用户密码,最多32位',
  `Uid` varchar(50) NOT NULL DEFAULT '14' COMMENT '上传的文件的属主,可以是系统用户id或者系统用户名,应该和web是同一个用户',
  `Gid` varchar(50) NOT NULL DEFAULT '5' COMMENT '上传的文件的属组,可以是系统用户组id或者系统用户组名,应该是和web是同一个系统用户组',
  `Dir` varchar(500) NOT NULL DEFAULT '' COMMENT 'ftp目录',
  `QuotaFiles` int(10) NOT NULL DEFAULT '0' COMMENT '最多文件数,0不限制',
  `QuotaSize` int(10) NOT NULL DEFAULT '100' COMMENT '空间大小单位MB,0不限制',
  `ULBandwidth` int(10) NOT NULL DEFAULT '128' COMMENT '上传带宽,0不限制,单位KB',
  `DLBandwidth` int(10) NOT NULL DEFAULT '128' COMMENT '下载带宽,,0不限制,单位KB',
  `Ipaddress` varchar(15) NOT NULL DEFAULT '*',
  `Comment` tinytext,
  `Status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '是否启用这个帐号,1启用,0禁用',
  `ULRatio` smallint(5) NOT NULL DEFAULT '0',
  `DLRatio` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`User`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8


下面我们插入一个管理员帐号：
假设/home/www/在系统里面的属主是www
用户名：ftprootadmin
密码：hellokitty
主目录是：/home/www/

INSERT INTO `users` VALUES ('ftprootadmin', 'hellokitty', 'www', ''www, '/home/www/', '0', '0', '0', '0', '*', '', '1', '0', '0');

提醒:
默认是明文密码，如果想使用其它加密方式如md5等，请修改etc/pure-ftpd.conf,
按着里面的说明修改：
MYSQLCrypt      md5
然后数据库存储加密的密码即可

最后我们启动pure-ftpd：
/server/pureftpd/ftpd start

然后使用ftp客户端连接测试吧，推荐Filezilla，如果乱码请在客户端里设置字符集为GBK