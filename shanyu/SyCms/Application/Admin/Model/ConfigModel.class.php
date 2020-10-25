<?php
namespace Admin\Model;
use Think\Model;

class ConfigModel extends Model{

    protected $_validate = array(
        
        array('title','require','请填写配置名称',1,'regex',3),
        array('name','','该标识已存在',1,'unique',1),
        array('value','require','请填写配置值',1,'regex',3),

    );

    public function getConfig($module='Admin'){
        $where="status=1 AND module='Common' OR module='{$module}'";
        $list=$this->field('name,value,type')->where($where)->select();
        $config=array();

        foreach ($list as $v) {
            switch ($v['type']) {
                case 11:
                    $config[$v['name']]=str_arr($v['value']);
                    break;
                case 9:
                    $config[$v['name']]=(bool)$v['value'];
                    break;
                default:
                    $config[$v['name']]=$v['value'];
                    break;
            }
        }
        return $config;
    }
}