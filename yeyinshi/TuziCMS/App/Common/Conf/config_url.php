<?php
return array(
		
		'URL_PATHINFO_DEPR'=>'/',//修改URL的分隔符
		'URL_CASE_INSENSITIVE' =>true,//实现URL访问不再区分大小写
		'URL_HTML_SUFFIX'=>'html|shmtl|xml', // 限制伪静态文件的后缀名
		'URL_MODEL'=>1,//url模式，0是普通模式，1是rwe模式，2是被书写模式，隐藏index.php入口文件
		'URL_ROUTER_ON'=>true,//开启url路由
		'URL_ROUTE_RULES'=>array(
				'/^Page\/group\/(\d+)$/'=>'Page/group?id=:1', //单页的url优化
					
				'/^Article\/group\/(\d+)$/'=>'Article/group?id=:1', //文章列表页的url优化
				'/^Article\/detail\/(\d+)$/'=>'Article/detail?id=:1', //文章详情页的url优化
					
				'/^Product\/group\/(\d+)$/'=>'Product/group?id=:1', //产品列表页的url优化
				'/^Product\/detail\/(\d+)$/'=>'Product/detail?id=:1', //产品详情页的url优化
					
				'/^Photo\/group\/(\d+)$/'=>'photo/group?id=:1', //相册列表页的url优化
				'/^Photo\/detail\/(\d+)$/'=>'photo/detail?id=:1', //相册详情页的url优化
					
				'/^Download\/group\/(\d+)$/'=>'Download/group?id=:1', //下载列表页的url优化
				'/^Download\/detail\/(\d+)$/'=>'Download/detail?id=:1', //下载详情页的url优化
		),

);
?>