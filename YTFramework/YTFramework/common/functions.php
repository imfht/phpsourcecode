<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 * @author     Tangqian<tanufo@126.com>
 * @version    $Id: functions.php 96 2016-04-25 02:39:00Z lixiaohui $
 * @created    2016-04-13
 * =============================================================================
 */

/**
 * 获取所有模块的对应的别名
 */
function ytf_getModuleAlias($moduleConfig)
{
    $modules = array();
    foreach ($moduleConfig as $alias => $value) {
        $name = $value;
        if (is_array($value) && isset($value['module'])) {
            $name = $value['module'];
        }
        $modules[$name] = $alias;
    }
    return $modules;
}
