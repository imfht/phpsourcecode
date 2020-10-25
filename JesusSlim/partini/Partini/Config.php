<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/28
 * Time: 下午11:04
 */

namespace Partini;


class Config
{

    protected $data;

    public function __construct()
    {
        $this->data = array(
            //default

            //cookie
            'COOKIE_DOMAIN'         =>  '',      // Cookie有效域名
            'COOKIE_PATH'           =>  '/',     // Cookie路径

        );
    }

    public function add($data){
        $this->data = $this->renderExtConfig(array_merge($this->data,$data));
    }

    public function get($key){
        return $this->data[$key];
    }

    protected function renderExtConfig($base_config){
        if(isset($base_config['EXT'])){
            $ext_files = is_array($base_config['EXT']) ? $base_config['EXT'] : explode(',',$base_config['EXT']);
            foreach ($ext_files as $f){
                if(file_exists($f)){
                    $config_in_f = require($f);
                    if(is_array($config_in_f)) $base_config = array_merge($base_config,$this->renderExtConfig($config_in_f));
                }
            }
            unset($base_config['EXT']);
        }
        return $base_config;
    }

    public static function read($key){
        return Application::getInstance()->getConfig($key);
    }
}