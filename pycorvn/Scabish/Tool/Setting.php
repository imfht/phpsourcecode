<?php 
namespace Scabish\Tool;

use SCS;

/**
 * 系统设置
 * 初始化时会一次性读取所有常用配置，不常用配置将会在调用的时候从数据库取出
 * 
 * @todo 缓存配置信息到文件中，周期性更新
 * @author keluo<pycorvn@yeah.net>
 * @since 2015-12-24 10:36:52
 */
class Setting {
    
    private static $_instance;
    private $_settings = [];
    
    const TYPE_TEXT = 1; // 文本
    const TYPE_DICT = 2; // 字典(数值索引+排序键)
    const TYPE_OBJECT = 3; // 对象(字符串索引)
    const TYPE_SET = 4; // 集合
    
    public function __construct() {}
    
    public static function Instance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            $settings = \Scabish\Abyss\Client::Instance()->Setting();
            foreach($settings as $setting) {
                self::$_instance->PushSetting($setting->fdKey, $setting->fdValue, $setting->fdType, $setting->fdDesc);
            }
        }
        return self::$_instance;
    }
    
    public function __clone() {}
    
    public function __get($key) {
        $data = $this->GetKey($key);
        if(is_null($data)) return null;
        switch($data['type']) {
        	case Setting::TYPE_DICT:
        	    $_value = unserialize($data['value']) ? : [];
        	    usort($_value, function($a, $b) { return $a['sort'] > $b['sort']; });
        	    $value = [];
        	    foreach($_value as $v) {
        	        $value[$v['key']] = $v['value'];
        	    }
        	    return $value;
    	    case Setting::TYPE_OBJECT:
    	        $_value = unserialize($data['value']) ? : [];
    	        $value = new \stdClass();
    	        // 提供访问支持：Setting::Instance()->object->property
    	        foreach($_value as $v) {
    	            $value->{$v['key']} = $v['value'];
    	        }
    	        return $value;
        	case Setting::TYPE_SET:
        	    return unserialize($data['value']) ? : [];
	        case Setting::TYPE_TEXT:
	            return $data['value'];
        }
    }
    
    /**
     * 获取指定key描述信息
     * @param string $key 字典索引
     * @return string
     */
    public function Desc($key) {
        $key = strtolower($key);
        return isset($this->_settings[$key]) ? $this->_settings[$key]['desc'] : '';
    }
    
    /**
     * 返回字典
     * @param string $key 字典索引
     * @param string $item 字典项
     * @param integer $length 返回数量
     * @return NULL|mixed
     */
    public function Dict($key, $item = false, $length = 0) {
        $data = $this->GetKey($key);
        if(!$data) return null;
        $data = unserialize($data['value']);
        if($length) $data = array_slice($data, 0, $length);
        if(!$data) return is_null($item) ? [] : null;
        usort($data, function($a, $b) { return $a['sort'] > $b['sort']; });
        $dict = [];
        foreach($data as $v) {
            $dict[$v['key']] = $v['value'];
        }
        if(false === $item) {
            return $dict;
        } else {
            return isset($dict[$item]) ? $dict[$item] : null;
        }
    }
    
    protected function GetKey($key) {
        $key = strtolower($key);
        if(!isset($this->_settings[$key])) {
            $setting = SCS::db()->select('fdKey, fdDesc, fdValue, fdType')->from('Setting')->where('fdKey = "'.$key.'"')->fetch();
            if($setting) {
                $this->PushSetting($setting->fdKey, $setting->fdValue, $setting->fdType, $setting->fdDesc);
            } else {
                $this->_settings[$key] = null;
            }
        }
        return is_null($this->_settings[$key]) ? null : $this->_settings[$key];
    }
    
    protected function PushSetting($key, $value, $type, $desc) {
        $this->_settings[strtolower($key)] = compact('value', 'type', 'desc');
    }
}