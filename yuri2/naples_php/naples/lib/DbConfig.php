<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/16
 * Time: 10:04
 */

namespace naples\lib;

//专门适配数据库配置
use naples\lib\base\Service;

/**
 * 数据库配置转换器
 * 将通用格式的配置数组,获得注入数据库驱动类的格式
 * 配置文件configs/dbConfig.php
 */
class DbConfig extends Service
{
    /**
     * 加载指定配置
     * @param $configName string
     * @return array
     */
    function load($configName='local'){
        $confArr=$this->config($configName);
        if (!is_array($confArr)){\Yuri2::throwException('Can not find Db options :'.$configName);}
        if (!isset($confArr['connection_string'])){
            switch ($confArr['type']){
                case 'sqlsrv':
//                    $dsn  =   'sqlsrv:Database='.$config['database'].';Server='.$config['hostname'];
                    $confArr['connection_string']="{$confArr['type']}:Server={$confArr['host']};Database={$confArr['dbname']}";
                    break;
                default:
                    $confArr['connection_string']="{$confArr['type']}:host={$confArr['host']};dbname={$confArr['dbname']}";
                    break;
            }
        }
        return $confArr;
    }

    /**
     * 读取所有配置
     * @return array
     */
    function loadAll(){
        $rel=[];
        foreach ($this->config() as $k=>$v){
            if ($k=='default'){continue;}
            $rel[$k]=$this->load($k);
        }
        return $rel;
    }

}