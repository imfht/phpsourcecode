<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'zs_home', //session前缀
    'COOKIE_PREFIX'  => 'zs_home_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',	//修复uploadify插件无法传递session_id的bug

);