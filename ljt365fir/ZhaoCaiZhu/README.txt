-------------------------------------------------------------------------
╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳
╳╳▓╳╳╳╳╳╳▓╳╳╳╳╳╳╳╳▓╳╳╳╳╳╳╳╳▓╳╳╳▓╳╳╳╳╳
╳╳▓╳╳╳╳╳▓╳╳╳╳╳╳╳╳▓╳▓╳╳╳╳╳▓╳▓╳╳╳▓╳╳╳▓╳
╳╳▓▓▓╳▓▓▓▓▓▓╳╳╳╳▓╳╳╳▓╳╳╳╳╳▓╳╳╳▓▓▓╳▓╳╳
╳▓╳╳╳╳▓╳╳╳╳▓╳╳╳▓╳╳╳╳╳▓╳╳╳▓╳▓╳╳╳▓╳▓╳╳╳
╳▓▓▓▓╳▓╳╳╳╳▓╳▓▓╳▓▓▓▓▓╳▓▓╳╳╳▓▓▓▓▓▓▓▓▓╳
╳╳▓╳╳╳▓╳╳╳╳▓╳╳╳╳╳╳▓╳╳╳╳╳╳╳╳▓╳╳╳▓╳╳╳╳╳
╳╳▓╳╳╳▓▓▓▓▓▓╳╳╳╳╳╳▓╳╳╳╳╳╳╳▓▓╳╳▓▓▓▓▓╳╳
╳▓▓▓▓╳▓╳╳╳╳▓╳╳▓▓▓▓▓▓▓▓▓╳╳▓╳▓╳▓▓╳╳╳▓╳╳
╳╳▓╳╳╳▓╳╳╳╳▓╳╳╳╳╳╳▓╳╳╳╳╳╳╳╳▓▓╳▓▓▓▓▓╳╳
╳╳▓╳▓╳▓╳╳╳╳▓╳╳╳▓╳╳▓╳╳▓╳╳╳╳╳▓╳╳▓╳╳╳▓╳╳
╳╳▓▓╳╳▓▓▓▓▓▓╳╳╳╳▓╳▓╳▓╳╳╳╳╳╳▓╳╳▓▓▓▓▓╳╳
╳╳▓╳╳╳▓╳╳╳╳▓╳▓▓▓▓▓▓▓▓▓▓▓╳▓▓╳╳╳▓╳╳╳▓╳╳
╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳╳
-------------------------------------------------------------------------
1、安装说明
访问 htttp://www.你的网站.com/install/index.php文件 按照提示操作即可完成安装！
特别注意 如果你的安装目录不是网站的根目录
请在 app/base/Constant.class.php中修改
const ROOT_DIR = "";
eg:你的访问路径是htttp://www.你的网站.com/smpss
const ROOT_DIR = "/smpss";
需要注意的就是几个文件夹的权限！
如果安装不成功 请查看 安装说明2

系统用途
主要用于中小网商以及中小个体经营户库存管理

功能简介
1.商品分类功能
2.商品信息管理功能
3.进货管理功能
4.销售管理功能
5.退货管理功能
6.会员卡管理功能
7.用户组权限分配功能
8.日志记录功能(前台暂无展示)
9.销售统计功能


安装说明2
如果你按照安装说明1没有成功安装那就用这个方法把！
1.使用数据库导入工具(如：phpmyadmin)导入smss.sql文件
2.修改数据库配置文件config/db.ini.php 文件
3.如果你的服务器不支持伪静态 请在app/base/Constant.class.php中设置REWRITE =false 默认为true
4.在app 下面建立模版缓存目录 v_t 权限必须是可读写
5.app\cache目录必须有可读写权限

确认以上操作你都进行了！
OK 访问把！默认的超级管理员帐号 用户名：admin 密码：admin
如果你没有安装成功！可以email给我！并且说明你的错误提示！我会在尽可能快的时间给你回复！

----------------------以上是@齐迹同学辛苦编写的，再次向他致以衷心的感谢----------------------
猪哥做的主要修改：
一、销售管理
1、增加了按销售类型查询销售记录
2、增加了手动选择销售的商品
3、增加了自定义销售价格和修改购买数量
4、增加了手工输入会员卡号（根据输入的内容自动查找最相似的会员，可以是会员卡号、姓名、身份证号和电话号码）
5、增加了会员积分根据积分比率自动积累的功能	
二、图表统计
1、把原有的统计图表换成了百度echarts http://www.oschina.net/p/echarts
2、完善了图表统计查询代码
三、会员管理
1、增加会员购买记录查询页面
2、增加会员兑换记录查询页面
3、增加会员退货记录查询页面
4、增加会员资料卡
5、增加了会员积分兑换功能
6、增加了快捷退货功能
四、系统设置
1、增加了模版修改
2、增加了积分兑换比率
3、重写了系统配置文件的保存方法
-------------------------------------------------------------------------
今后猪哥会以招财猪的名称继续更新这个项目，如同齐迹所说，这个软件针对的是小本经营的个体户。所以不能以成熟的ERP软件来衡量她的好坏。我决定继续完善她是因为我把改好的软件给我姑妈店里装了一套。所以以后还会更新，而我开发的思路也是根据我姑妈的经营方式来做的，各位亲们如果有啥好的想法咱们可以一起讨论讨论。
我的PHP功底也就是2的水平，代码难免会有问题，如果亲在阅读的时候有什么意见或建议，请与我联系，我的联系方式如下：
网易邮箱：ljt365fir@126.com
GMail：ljt365fir@gmail.com
OSC地址：http://my.oschina.net/bojinzhu
个人博客：http://www.bojinxiaozhu.com
