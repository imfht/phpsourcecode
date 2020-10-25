# WordPress转换程序

**注意：转换程序会清除SYBlog已有数据**

* 全新安装SYBlog（注意：需要和WordPress处于同一数据库）

* 修改config.php

	1.修改数据库相关配置
	
	2.如果原来的WordPress使用了“自定义链接”（slug），请将`'slug' => FALSE`改为`'slug' => TRUE`

* 若数据量大，建议在cli模式下运行convert.php。您也可以用浏览器打开convert.php

* 若WordPress中使用了自定义链接，请在config.php中开启slug，并修改重写规则

	将原WordPress文章URL重写至index.php?r=home/wordpress&type=article&slug=自定义地址
	
	例如原WordPress文章URL为/archives/what-is-markdown.html，则重写至index.php?r=home/wordPress&type=article&slug=what-is-markdown
	
	将原WordPress分类URL和标签URL重写至index.php?r=home/wordPress&type=meta&slug=自定义地址&page=页码
	
	例如原WordPress分类URL为/category/myarticle/1/，则重写至index.php?r=home/wordPress&type=meta&slug=myarticle&page=1