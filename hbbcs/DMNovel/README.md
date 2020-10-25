# 本项目已不维护，新项目已移至 https://gitee.com/hbbcs/new_DMNovel
#東木书屋 DMNovel

本人喜欢在业余时间阅读小说（当然正版、盗版都有），许多的小说网站广告、限制很多，就想自己搞个无广告的网站。从网上DOWN了许多程序，要么不符合自己的要求，要么加密无法修改，因为工作原因，所以就搁置下来。最近业务不是太多，就决定自己写个程序。我们的口号：

无广告 无加密 可上传 可盗取

#更新
新增用户注册、书架
已安装过程序的请执行install/update.php中的SQL语句，并将theme/images/avatar目录设为可写。同时注册一个新用户，在mysql后台将此用户的level设为9，这个用户就是管理员帐号。

#功能

- 可以上传txt文本文件，自动获取书名、作者名、描述，自动将文本分章节保存
- 每个章节在阅读一次后缓存
- 可以从 顶点小说 抓取小说 （暂时只支持这个，如果有时间可能会改）
- 可以发布自己的小说:) （这个我喜欢，没事自己写些乱七八糟的东西）
- 章节进行分页，不再一个页面到底，使用鼠标、左右按键进行翻页（低版本的IE不支持，让我们一起消灭它们）
- 支持手机浏览，点击翻页
- 其他一时想不起来了...

#安装

- 打开地址http:// you ip /dmnovel/install/
- 正式运行环境请将/index.php中的define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development'); 改为define('ENVIRONMENT', 'production');

#截图

前台

![首页](http://git.oschina.net/uploads/images/2017/0112/085317_4f5c4e2c_62743.png  "首页")
![小说页面](http://git.oschina.net/uploads/images/2017/0112/085410_eb4d41d6_62743.png "小说页面")
![分类列表](http://git.oschina.net/uploads/images/2017/0112/090034_ef54c95e_62743.png "分类列表")
![章节页面](http://git.oschina.net/uploads/images/2017/0112/085518_63c33f01_62743.png "章节页面")
![登录](http://git.oschina.net/uploads/images/2017/0112/085610_7d506681_62743.png "登录")
![书架](http://git.oschina.net/uploads/images/2017/0112/085723_1f6ca670_62743.png "书架")

后台

![发布小说](http://git.oschina.net/uploads/images/2016/0412/171338_6f94a5e9_62743.png "发布小说")
![上传TXT小说](http://git.oschina.net/uploads/images/2016/0412/171424_1b43062e_62743.png "上传TXT小说")
![采集小说](http://git.oschina.net/uploads/images/2016/0412/171510_d444ba02_62743.png "采集小说")