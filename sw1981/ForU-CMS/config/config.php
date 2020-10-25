<?php
define('DATA_TYPE', 'mysql');
define('SQL_DIR', 'sql');
define('ADMIN_DIR', 'admin');
define('ADDON_DIR', 'addon');
define('TPL_DIR', 'template');
define('LANG_DIR', 'language');
define('FONT_DIR', 'font');
define('DATA_DIR', 'data');
define('SUBSCRIBE_DIR', 'subscribe');
define('ADMIN_LOG', false);
define('SITE_SUB', false);
// token
define('TOKEN_ON', false); // token开启为true
define('TOKEN_NAME', 'token');
// 上传图片限制大小
define('UPLOAD_LIMIT', true);
define('UPLOAD_IMG', 600);
// 验证码
define('VERIFYCODE_WIDTH', 150);
define('VERIFYCODE_HEIGHT', 50);
// 初始化页面
define('REWRITE', false);
define('CART', false); // 对应购物车cls.cart类使用及运费的后台配置
define('COOKIE_TIMEOUT', 15 * 60 * 60); //超时
define('COOKIE_EXPIRE', 30 * 24 * 3600); // cookie超期时间(天*时*秒)
define('COOKIE_DOMAIN', 'localhost');
// 后台无须验证的页面
define('ADMIN_EXCLUDE', '["cms_login.php","verifycode.php","index.php"]');

// 时差
$GLOBALS['timezone'] = 8;

// 默认分页结构
define('PAGE_STRUCTURE', '["<li><a href=\"","1.html\" title=\"\u9996\u9875\">&laquo;<\/a><\/li>","<li class=\"am-disabled\"><a href=\"javascript:;\" title=\"\u4e0a\u4e00\u9875\">&lsaquo;<\/a><\/li>",".html\" title=\"\u4e0a\u4e00\u9875\">&lsaquo;<\/a><\/li>","<li class=\"am-active\"><a href=\"javascript:;\">","<\/a><\/li>",".html\" title=\"\u7b2c","\u9875\">",".html\" title=\"\u5c3e\u9875\">&raquo;<\/a><\/li>","<li class=\"am-disabled\"><a href=\"javascript:;\" title=\"\u4e0b\u4e00\u9875\">&rsaquo;<\/a><\/li>",".html\" title=\"\u4e0b\u4e00\u9875\">&rsaquo;<\/a><\/li>","=1\" title=\"\u9996\u9875\">&laquo;<\/a><\/li>","\" title=\"\u4e0a\u4e00\u9875\">&lsaquo;<\/a><\/li>","\" title=\"\u7b2c","\" title=\"\u5c3e\u9875\">&raquo;<\/a><\/li>","\" title=\"\u4e0b\u4e00\u9875\">&rsaquo;<\/a><\/li>","<li class=\"am-active\"><a href=\"javascript:;\">","<\/a><\/li>"]');
