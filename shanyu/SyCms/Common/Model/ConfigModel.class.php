<?php
namespace Common\Model;
use Think\Model;

class ConfigModel extends Model{

    protected $_validate = array(
        array('title','require','请填写配置名称',1,'regex',3),
        array('name','','该标识已存在',1,'unique',1),
        array('value','require','请填写配置值',1,'regex',3),
    );

    public function getConfig($module=''){
        if(empty($module)){
            $where="status=1 AND module='Common'";
        }else{
            $where="status=1 AND (module='Common' OR module='{$module}')";
        }
        $list=$this->field('name,value,type')->where($where)->select();
        $config=array();
        foreach ($list as $v) {
            switch ($v['type']) {
                case 11:
                    //数组
                    $config[$v['name']]=str_arr($v['value']);
                    break;
                case 9:
                    //布尔
                    $config[$v['name']]=(bool)$v['value'];
                    break;
                default:
                    //字符串
                    $config[$v['name']]=$v['value'];
                    break;
            }
        }
        return $config;
    }

    public function getModule(){
        $result[]='Common';
        $Dir=dir(APP_PATH);
        while($_file=$Dir->read()){
            if($_file !='.' && $_file!='..'){
                $result[]=$_file;
            }
        }
        $Dir->close();
        return $result;
    }

    protected function _after_insert($data,$options){
        F('AdminConfig',NULL);
    }
    protected function _after_update($data,$options){
        F('AdminConfig',NULL);
    }
    protected function _after_delete($data,$options) {
        F('AdminConfig',NULL);
    }

}