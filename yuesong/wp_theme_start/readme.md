![wordpress_theme_strar](screenshot.png)

#主题开始的地方


##修改默认设置项目(settings.php)：
 * 1、禁用google字体，优化后台速度
 * 2、修改wordpress后台底部文字
 * 3、移除顶部栏的wordpress logo，评论logo，更新logo
 * 4、去除wp_head()无关紧要的代码，包括XML-RPC、 Windows Live Writer、WordPress Generator、和日志相关的 Link
 * 5、移除WordPress版本号


##内置函数(inc/function.php)：
  * 1、输出摘要 the_abstract(string $string,int $num);
      	如：the_abstract(get_the_content(),200);

  * 2、获取最新文章 the_last_posts(int $showposts,array $options = array());
  		如：the_last_post(10);
  * 3、获取随机文章 the_random_posts(int $showposts,array $options = array())
  		如：the_random_posts(10);


##关于作者
在使用中有任何问题，欢迎反馈给我，可以用以下方式跟我交流

*  邮件 ：liuyuesongde@163.com
*  QQ ：893521870
*  站点留言：http://eyaslife.com