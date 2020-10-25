<?php
/*
 *  2017年2月14日 星期二
 *  系统管理全局业务逻辑
*/
namespace app\Server;
use hyang\Logic;
use think\Db;
class Admin extends Logic
{
    private $_getLisaVarJson;
    // 获取系统模块中-当前系统配置文件 使Lisa页面更具通用性
    public function getLisaConfig(){
        return config('lisa_ieads_file').md5('lisa.ieads').'.bsj';
    }
    // 获取系统模块的系统参数值
    public function getLisaVar($key=null){
        $data = empty($this->_getLisaVarJson)? bsjson(file_get_contents($this->getLisaConfig())):$this->_getLisaVarJson;
        $data = is_array($data)? $data:[];
        if($data && empty($this->_getLisaVarJson)) $this->_getLisaVarJson = $data;
        if($key){
            if(is_array($key)){
                $retArray = [];
                foreach($key as $k=>$v){
                    $key = intval($k)? $v:$k;
                    $retArray[$key] = array_key_exists($v,$data)? $data[$v]:'';
                }
                return $retArray;
            }
            return array_key_exists($key,$data)? $data[$key]:'';
        }
        return $data;
    }
}