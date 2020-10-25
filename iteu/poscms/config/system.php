<?php

/**
 * v3.1.0
 */

/**
 * 系统配置文件
 */

return array(

	'SYS_LOG'                       => 0, //后台操作日志开关
	'SYS_KEY'                       => 'testtesttest', //安全密钥
	'SYS_DEBUG'                     => 0, //调试器开关
	'SYS_HTTPS'                     => '', //HTTPS安全模式
	'SYS_HELP_URL'                  => '', //系统帮助url前缀部分
	'SYS_EMAIL'                     => 'admin@admin.com', //系统收件邮箱，用于接收系统信息
	'SYS_REFERER'                   => '', //来路字符串
	'SYS_MEMCACHE'                  => 0, //Memcache缓存开关
	'SYS_ATTACHMENT_DIR'            => '', //系统附件目录名称
	'SYS_ATTACHMENT_DB'             => 0, //附件归档存储开关
	'SYS_UPLOAD_DIR'                => 'uploadfile', //附件上传目录
	'SYS_CATE_SHARE'                => '', //共享栏目展示方式
	'SYS_ATTACHMENT_URL'            => '', //附件域名设置
	'SYS_CRON_QUEUE'                => 0, //任务队列方式
	'SYS_CRON_NUMS'                 => 20, //每次执行任务数量
	'SYS_CRON_TIME'                 => 300, //每次执行任务间隔
	'SYS_ONLINE_NUM'                => 1000, //服务器最大在线人数
	'SYS_ONLINE_TIME'               => 7200, //会员在线保持时间(秒)
	'SYS_TEMPLATE'                  => '', //网站风格目录名称
	'SYS_THUMB_DIR'                 => '', //缩略图目录
	'SYS_NAME'                      => 'FineCMS', //
	'SYS_CMS'                       => 'FineCMS高级版', //
	'SYS_NEWS'                      => 1, //
	'SYS_SYNC_ADMIN'                => 0, //后台同步登录开关
	'SYS_DOMAIN'                    => '', //后台域名
	'SYS_THEME_DOMAIN'              => '', //风格域名
	'SYS_UPDATE'                    => 1, //兼容升级开关
	'SYS_AUTO_CACHE'                => '', //自动缓存
	'SITE_EXPERIENCE'               => '经验值', //经验值名称
	'SITE_SCORE'                    => '虚拟币', //虚拟币名称
	'SITE_MONEY'                    => '金钱', //金钱名称
	'SITE_CONVERT'                  => 10, //虚拟币兑换金钱的比例
	'SITE_ADMIN_CODE'               => 0, //后台登录验证码开关
	'SITE_ADMIN_PAGESIZE'           => 8, //后台数据分页显示数量
	'SYS_GEE_CAPTCHA_ID'            => '', //极验验证ID
	'SYS_GEE_PRIVATE_KEY'           => '', //极验验证KEY
	'SYS_CACHE_INDEX'               => 300, //站点首页静态化
	'SYS_CACHE_MINDEX'              => 300, //模块首页静态化
	'SYS_CACHE_MSHOW'               => 300, //模块内容缓存期
	'SYS_CACHE_MSEARCH'             => 300, //模块搜索缓存期
	'SYS_CACHE_SITEMAP'             => 300, //Sitemap.xml更新周期
	'SYS_CACHE_LIST'                => 300, //List标签查询缓存
	'SYS_CACHE_MEMBER'              => 300, //会员信息缓存期
	'SYS_CACHE_ATTACH'              => 300, //附件信息缓存期
	'SYS_CACHE_FORM'                => 300, //表单内容缓存期
	'SYS_CACHE_POSTER'              => 300, //广告内容缓存期
	'SYS_CACHE_SPACE'               => 300, //会员空间内容缓存期
	'SYS_CACHE_TAG'                 => 300, //Tag内容缓存期
	'SYS_CACHE_COMMENT'             => '', //评论统计缓存期
	'SYS_CACHE_PAGE'                => '', //单页静态化

);