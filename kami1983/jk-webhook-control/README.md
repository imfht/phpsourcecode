
# jk-webhook-control version 1.1.0

* 初期版本仅用来处理接收http://git.oschina.net 的WebHook 请求，完成简单的服务器部署。
* version 1.0.3 已经测试并可以处理oschina 的 WebHook 请求。对于其他暂不进行开发。
* 不要忘记更改当前git 项目的目录权限 chown -R apache.apache . 以便程序可以调用处理。
* 注意git 安装目录，这里举例安装在 /usr/bin/git 

#Version 1.1.0
* 整合所有管理页面到management.php 中
* 增加命令触发页面 order-list.php 该页面用于显示已经定义的命令并且支持web直接触发。

#Version 1.0.3
* 已经完成对接OsChina Git 的web hook 处理


# Install & Helps

* 下载代码。
* 配置站点目录，为代码目录。
* copy conf/setting.inc.php.sample 为 conf/setting.inc.php 
* 修改 conf/setting.inc.php 

    $___conf_arr[]=array('__source'=>'oschina', //Not use on verson 1.1.0 
                                '__order'=>'ls', //Your order. such as cd /git-project-dir ;/usr/bin/git pull -v --progress
                                '__title'=>'Git Pull', //用于显示对该命令的描述
                                '__manual_execute_pwd'=>'111111',  //定义手工执行时所需的密码
                                'password'=>'Hook-password if has.',
                                'repository-name'=>'Your repository name such as jk-webhook-control',);

    return $___conf_arr;

* 配置oschina.net 的webhook 为 http(s)://your-site/post-landing.php 
* Install and config done.

* 为了安全起见应该注释掉 post-landing.php 23行CWebhookLog::AppendLog('RECIVE POST STR:'.date("Y-m-d H:i:s"), $match_arr[1]); 该行可能泄露密码信息

# Example of setting.inc.php 
* 例子中的配置了一些命令用于在web上完成命令切换。

<?php

$___conf_arr=array();

$___conf_arr[]=array('__source'=>'oschina', 
                                '__order'=>'cd /var/www/SITE_DEVELOPERS/SITE_JK_COMPONENT/kt-shop;git pull -v --progress;', 
                                '__title'=>'Kt-Shop Git Pull',
                                '__manual_execute_pwd'=>'******',
                                'password'=>'kami2259',
                                'repository-name'=>'kt-shop',);


$___conf_arr[]=array('__source'=>'oschina',
                               '__order'=>'cd /var/www/SITE_DEVELOPERS/SITE_JK_COMPONENT/kt-shop;git checkout master;',
                                '__title'=>'Kt-Shop Git Checkout Master',
                                '__manual_execute_pwd'=>'******',);


$___conf_arr[]=array('__source'=>'oschina',
                               '__order'=>'cd /var/www/SITE_DEVELOPERS/SITE_JK_COMPONENT/kt-shop;git checkout develop;',
                                '__title'=>'Kt-Shop Git Checkout Develop',
                                '__manual_execute_pwd'=>'******',);

return $___conf_arr;

