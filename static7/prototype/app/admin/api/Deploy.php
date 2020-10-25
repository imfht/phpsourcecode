<?php

namespace app\admin\api;

use think\Model;

/**
 * Description of UserApi
 * 应用的系统配置API
 * @author static7
 */
class Deploy extends Model {

    /**
     * 获取数据库中的配置列表
     * @return array 配置数组
     */
    public function lists() {
        $data = $this::all(function($query) {
                    $query->where(['status' => 1])->field('type,name,value');
                });
        $config = [];
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[strtolower($value->data['name'])] = self::parse($value->data['type'], $value->data['value']);
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     */
    private function parse($type, $value) {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
            case 5:
                dump($value);
//                die;
                break;
        }
        return $value;
    }

}
