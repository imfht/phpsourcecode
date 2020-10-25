<?php
namespace app\cms\index\wxapp;

use app\common\controller\index\wxapp\Show AS _Show; 

//小程序 显示内容
class Show extends _Show
{
    /**
     * 调取显示内容主题
     * @param number $id 内容ID
     * @return \think\response\Json
     */
    public function index($id=0){
        return parent::index($id);
    }
    
}













