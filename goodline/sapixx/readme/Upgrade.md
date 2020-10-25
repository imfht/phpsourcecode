# 升级指导

**环境要求PHP7.1+**
**升级前请注意备份站点和数据库!!!**

####备份以下目录
	/public/static.php
	/public/res
	/runtime/cert

####开启调试模式

	/app/app.php
    APP_DEBUG = true
####数据库配置

	/config/database.php
    [DATABASE]
    TYPE = mysql
    HOSTNAME = 127.0.0.1 #数据库连接地址
    DATABASE = test #数据库名称
    USERNAME = username #数据库登录账号
    PASSWORD = password #数据库登录密码
    HOSTPORT = 3306 #数据库端口
    CHARSET = utf8
    DEBUG = true