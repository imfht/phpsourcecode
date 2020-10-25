<?php

namespace Admin\Model;
use Think\Model;
/**
 * 配置模型
 */

class ConfigModel extends Model {
    protected $_validate = array(
        array('tname', 'require', '表名不能为空', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
        array('tname', '', '表名已经存在', self::VALUE_VALIDATE, 'unique', self::MODEL_BOTH),
        array('tnamec', 'require', '别称不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('tflag', 0),
        array('ttype', 0),
    );

    /**
     * 获取配置列表
     * @return array 配置数组
     */
    public function lists(){
        $map    = array('status' => 1);
        $data   = $this->where($map)->field('type,name,value')->select();
        
        $config = array();
        if($data && is_array($data)){
            foreach ($data as $value) {
                $config[$value['name']] = $this->parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

}
