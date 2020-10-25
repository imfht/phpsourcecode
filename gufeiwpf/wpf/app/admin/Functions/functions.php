<?php
/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    global $di;
    $list = $di->get("config")->CONFIG_TYPE_LIST->toArray();
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    global $di;
    $list = $di->get("config")->CONFIG_GROUP_LIST->toArray();
    return $list[$group];
}

 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}