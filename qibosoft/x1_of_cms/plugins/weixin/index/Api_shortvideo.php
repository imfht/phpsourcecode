<?php
namespace plugins\weixin\index;


class Api_shortvideo extends Api
{
    //唯一入口
    public function execute(){
        parent::execute();          //不能缺少的，实现权限判断
        $this->run_model();     //执行多个插件或模块里边的应用，方便扩展，当然也可以在这里写执行语句
    }
    
}