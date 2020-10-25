
#安装前说明
 * 安装之前一定要把数据库改成自己的数据库配置，否则运行yii命令的时候会造成失败问题
   ![IMG_0874](http://git.oschina.net/uploads/images/2016/0131/133640_2d5f0be6_568633.png)

##Yii2结合rbac做的一个简单的后台权限管理系统，界面采用的是MaterialAdmin一套模板。

>第一步

 * 使用yii的console命令运行命令 ：yii migrate --migrationPath=@yii/rbac/migrations/

>第二步

 * 继续运行命令 yii migrate

##数据表和数据已经填充完毕

>第三步

 * 配置虚拟机 运行地址xxx.com/auth/login,就可以到登录界面,说明虚拟目录必须配置到 :Yii-backadmin\backend\web
 * 用户名 admin
 * 密码   123456

>示例图展示

 * 登录图

   ![IMG_0874](http://git.oschina.net/uploads/images/2016/0128/180454_f19dd194_568633.png)

 * 主页面图

   ![IMG_0874](http://git.oschina.net/uploads/images/2016/0128/180125_1b31fa5f_568633.png)

 ##有问题反馈
 在使用中有任何问题，欢迎反馈给我，可以用以下联系方式跟我交流

 * 邮件(394703554@qq.com)
 * QQ: 394703554

 ##感谢大家对我的支持，谢谢大家，这个项目我会持续更新，希望大家关注。