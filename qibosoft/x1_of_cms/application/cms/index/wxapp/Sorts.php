<?php
namespace app\cms\index\wxapp;

use app\common\controller\index\wxapp\Sorts AS _Sort;

//小程序获取栏目信息
class Sorts extends _Sort
{
    /**
     * 获取栏目数据
     * @return \think\response\Json
     */
    public function index(){
        return parent::index();
    }
    
    public function hot(){
        return parent::hot();
    }

}













