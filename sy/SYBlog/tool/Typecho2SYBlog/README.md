# Typecho转换程序

* 全新安装SYBlog（注意：需要和Typecho处于同一数据库）

* 修改config.php

	1.修改数据库相关配置
	
	2.如果原来的Typecho使用了“自定义链接”（slug），请将`'slug' => FALSE,`改为`'slug' => TRUE,`
	
	3.填入您附件所在的域名，例如`http://cdn.example.com`，结尾不加“/”。如果您的附件和博客在同一域名下，请填写您博客的域名。例如`'attachmentDomain' => 'http://example.com'`
	
	**PS：如果您的博客不在网站根目录，请加上您的目录，例如`http://example.com/myblog`**

* 若数据量大，建议在cli模式下运行convert.php。您也可以用浏览器打开convert.php

* 若Typecho中使用了自定义链接，请在config.php中开启slug，并修改重写规则

	将原Typecho文章URL重写至index.php?r=home/typecho&type=article&slug=自定义地址
	
	例如原Typecho文章URL为/archives/what-is-markdown.html，则重写至index.php?r=home/typecho&type=article&slug=what-is-markdown
	
	将原Typecho分类URL和标签URL重写至index.php?r=home/typecho&type=meta&slug=自定义地址&page=页码
	
	例如原Typecho分类URL为/category/myarticle/1/，则重写至index.php?r=home/typecho&type=meta&slug=myarticle&page=1