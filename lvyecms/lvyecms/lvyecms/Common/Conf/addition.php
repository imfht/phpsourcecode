<?php

// +----------------------------------------------------------------------
// | LvyeCMS 高级配置
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.lvyecms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 旅烨集团 <web@alvye.cn>
// +----------------------------------------------------------------------

/**
 * 用户扩展配置
 */
defined('THINK_PATH') or exit();
return array(
    //云平台开关
    'CLOUD_ON' => false,
    'COOKIE_EXPIRE' => 3600, // Coodie有效期
    'COOKIE_DOMAIN' => '', // Cookie有效域名
    'COOKIE_PATH' => '/', // Cookie路径
    'SESSION_AUTO_START' => true, // 是否自动开启Session
    'SESSION_OPTIONS' => array(), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_PREFIX' => '', // session 前缀
    'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
    'DEFAULT_AJAX_RETURN' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_FILTER' => 'htmlspecialchars', // 默认参数过滤方法 用于 $this->_get('变量名');$this->_post('变量名')...
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'DATA_CACHE_TYPE' => 'File', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_SUBDIR' => false, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    /* 错误设置 */
    'ERROR_MESSAGE' => '您浏览的页面暂时发生了错误！请稍后再试～', //错误显示信息,非调试模式有效
    'ERROR_PAGE' => '', // 错误定向页面
    'SHOW_ERROR_MSG' => false, // 显示错误信息
    /* URL设置 */
    'URL_CASE_INSENSITIVE' => false, // 默认false 表示URL区分大小写 true则表示不区分大小写
    /**
     * 水平凡提示：
     * 不建议修改全局配置的URL模式。
     * 如果会员中心需要相应模式，请在独立项目下增加Conf/config.php的方式配置。
     * 其他模块也是如此。
     * 内容模块（Contents）强烈不建议设置。
     */
    'URL_MODEL' => 0, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR' => '/', // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX' => '.html', // URL伪静态后缀设置
    /* 表单令牌 */
    'TOKEN_ON' => false, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5

    /* 分页配置 */
    "PAGE_LISTROWS" => 20, //分页数
    //默认分页模板
    "PAGE_TEMPLATE" => '<span class="all">共有{recordcount}条信息</span><span class="pageindex">{pageindex}/{pagecount}</span>{first}{prev}{liststart}{list}{listend}{next}{last}',
    "VAR_PAGE" => 'page', //当前分页变量 page=2 page=3
    'DEFAULT_MODULE' => 'Content', // 默认模块
    //函数加载
    'LOAD_EXT_FILE' => 'extend',
);
