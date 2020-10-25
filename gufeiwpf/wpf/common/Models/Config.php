<?php
namespace Wpf\Common\Models;
class Config extends \Wpf\Common\Models\CommonModel{
    
    public function initialize(){
        parent::initialize();
    }
    
    public function onConstruct(){
        parent::onConstruct();
    }
    
    
    public function validation(){

        $this->validate(new \Phalcon\Mvc\Model\Validator\Uniqueness(
            array(
                "field"   => "name",
                "message" => "配置标识重复"
            )
        ));
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"   => "name",
                "message" => "配置标识必须填写"
            )
        ));
        
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(
            array(
                "field"   => "title",
                "message" => "配置标题必须填写"
            )
        ));


        return $this->validationHasFailed() != true;
    }
    
    public function beforeCreate(){
        $this->create_time = time();
    }
    
    public function beforeSave(){
        $this->update_time = time();
        $this->status = 1;
    }
    
    
    /**
     * 获取配置列表
     * @return array 配置数组
     * @author 吴佳恒
     */
    public function lists(){
        //$map    = array('status' => 1);
        //$data   = $this->where($map)->field('type,name,value')->select();
        
        $data = $this->find(array(
            "conditions" => "status=1",
            "columns" => 'type,name,value'
        ))->toArray();
        
        
        $config = array();
        if($data && is_array($data)){
            foreach ($data as $value) {
                $config[$value['name']] = $this->parse($value['type'], $value['value']);
            }
        }
        return $config;
    }
    
    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     * @author 吴佳恒
     */
    private function parse($type, $value){
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value,':')){
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                }else{
                    $value =    $array;
                }
                break;
        }
        return $value;
    }

}