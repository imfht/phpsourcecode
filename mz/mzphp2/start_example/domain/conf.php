<?php
# 这个文件用于站群泛域名解析
# 返回对应要加载的文件，或者直接 改写 core::$conf 全局配置
# 以下仅是例子
function domain_init() {
    // 子域名门户
    if (!isset($_SERVER['HTTP_HOST'])) {
        return NULL;
    }
    $host       = $_SERVER['HTTP_HOST'];
    # 当域名为 abc.123.com 时，加载 www.mzphp.com 配置
    if($host == 'abc.123.com'){
    	return ROOT_PATH.'domain/www.mzphp.com.php';
    }else if($host == 'bbb.123.com'){
    	# 当域名为 bbb.123.com 时，加载 m.mzphp.com 配置
    	core::$conf['ismobile'] = 1;
    	return ROOT_PATH.'domain/m.mzphp.com.php';
    }
}
