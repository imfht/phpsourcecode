<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */

namespace Common\Api;
class ConfigApi {
    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     */
    public static function lists(){
       
        $data   = M('Config')->select();
        
        $config = array();
        if($data && is_array($data)){
            foreach ($data as $value) {
                $config[$value['name']] =$value['value'];
            }
        }
        return $config;
    }


}