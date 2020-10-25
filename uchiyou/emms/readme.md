##企业物资管理系统
###简介

企业单位通常需要管理大量的设备，建立物资管理系统可以有效地节约人力物力资源，并提高管理效率。一个企业物资管理系统应具有以下功能：

- 
-     1.实现物资的购入、登记、报废等管理；
-     2.可将各类物资分配到企业各个科室以便使用；
-     3.可按照物资类别，名称，价格、科室等查询、统计；
-     4.可生成相应的统计报表；
-     5.个人申报功能；其他说明、限制：所管理的物资分两大类：固定资产（如家具、电器）、耗材（文具等）；每一件固定资产有唯一的资产编号；物资管理员可以完成以上1、2、3、4功能，而普通员工只可查询本人、本科室相关的情况；

###技术结构
    主要框架 : PHP7 + laravel5.4 +　mysql5.5.36 + composer1.3.2(依赖管理)
    前端 : jquery + bootstrap + jstree（树形结构） + echart（图表） + layer（弹出层）
    其他：  阿里大于短信等


###完成概况 
 项目演示地址   http://39.108.228.215/

测试账号

        企业管理员 uchiyou@sina.com（希望不要删除太多数据）
        部门管理员 nash@sina.com
        普通员工 ali@sina.com
        密码都是 123456（希望手下留情不要改）



 **项目主页** 


![输入图片说明](https://git.oschina.net/uploads/images/2017/0430/150209_bee4f761_1030765.gif "在这里输入图片标题")

 **购买审批**  
-    1 待审批通知
-    2 历史记录
-    3 导出到 excel
-    4 打印当前表单

![输入图片说明](https://git.oschina.net/uploads/images/2017/0430/150249_bd6f2d02_1030765.png "在这里输入图片标题")


**搜索功能** 
- 根据名称，类别，价格和部门搜索物资信息

![](https://git.oschina.net/uploads/images/2017/0430/150346_721b67a7_1030765.png "搜索物资信息")


 **树形结构管理** 
- 以符合公司组织结构的树形结构管理物资。
- 可在树形目录上点击右键增删改查和拖拽移动节点，符合现实中的人事调动和物资分配。
- 每个部门可以有部门管理员管理所在分支，职责划分明确。
- 用 jquery 实现懒加载分支信息，减小服务器负载压力。

![输入图片说明](https://git.oschina.net/uploads/images/2017/0430/150546_a378f1f5_1030765.png "在这里输入图片标题")

 **统计** 
- 为系统记录提供图形化统计

![输入图片说明](https://git.oschina.net/uploads/images/2017/0430/150622_1f209bae_1030765.png "预约Top10统计")

其他：

- 公司树权限 ： 部门管理员，只能看到自己所在部门的物资记录。
            普通员工删除的记录，部门管理员可见；部门管理员删除的记录，公司树的顶级管理员可见。
- 系统权限 ：　软件即服务的理念（SaaS），系统管理员可以停止和恢复对一家公司的服务。（待完善）
- 提供站内消息和短信消息两种通知
- 为租借提供可选的外送服务
- 提供预约功能，当物资可用时，有消息通知
- 提供维修功能等

###目录结构

- 典型的laravel项目
- 需要在 .env 中添加阿里大于的短信验证第三方秘钥，如果需要发送邮件功能，也需要自行配置。

###项目部署

- 1、拉代码
        $ git clone http://git.oschina.net/uchiyou/emms
- 2、安装依赖
        $ composer install --optimize-autoloader --no-dev
        $ composer dump-autoload --optimize
- 3、清理
        $ php artisan clear-compiled
        $ php artisan optimize
- 4、数据库迁移 （需要先配置好数据库，参考 [laravel数据迁移](http://d.laravel-china.org/docs/5.4/migrations)）$ php artisan migrate(后面的版本将上传sql文件，不熟悉migrate同学可以将sql文件导入数据库)

- 5、
    1、cp .env.example .env , 在 .env 中配置短信，邮箱等账号信息；
    2、 确保storage目录下有如app，framework，views三个目录。
        确保storage/framework目录下也有cache，sessions，views三个目录。缺少以上目录就手动创建，然后赋予写的权限。
        `chmod 777 -R storage/logs`
        `chmod 777 -R bootstrap/cache` 


- 6、
    1、启动php解析器自带的服务器 
        php -S localhost:8080(url)

    2、使用 nginx + php-fpm 时，nginx 配置如下

```
server {
    listen       [::]:80;
    server_name  39.108.228.215;
    root        /var/www/emms/public;

    #设置加载 css flash txt js 图片等资源
     location ~* ^.+\.(jpg|jpeg|gif|png|bmp|css|js|swf|txt)$ {
        access_log off;
        break;
    }
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
        index index.php index.html index.htm;
    }

    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   /usr/share/nginx/html;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9002;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
``` 


**注意：** 出于作者账号保护和费用等原因，本项目并没有提供 发送短信的秘钥以及作者的邮箱密码。需要开发者自行配置。

 **部署到linux上可能遇到的问题：**

- 步骤2 出现 out of memory 类似问题。

    １　系统内存不够，关闭一些程序。 composer install 很吃内存。

    composer的其他问题参考 https://getcomposer.org/doc/articles/troubleshooting.md


- 访问的时候如果出现 ```
Please provide a valid cache path.
``` 的异常提示。解决方法：

    1、确保storage目录下有如app，framework，views三个目录。

    2、确保storage/framework目录下也有cache，sessions，views三个目录。缺少以上目录就手动创建，然后赋予写的权限。

    `chmod 777 -R storage/logs`
    `chmod 777 -R bootstrap/cache` 

    否则会发生`TokenMismatchException in VerifyCsrfToken.php line 68:异常

其他异常通常可以通过nginx 和 php 的日志定位问题。 


###关于作者
    作者为能力有限,有需求改进或程序有不规范之处，欢迎指出。
    如需深度定制或者二次开发技术支持，可以发送邮件至1373918920@qq.com 联系作者。
    