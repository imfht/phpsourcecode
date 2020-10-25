<?php
namespace app\cms\index\wxapp;

use app\common\controller\index\wxapp\Index AS _Index; 

//小程序显示内容
class Index extends _Index
{
    /**
     * 首页列表数据
     * @param number $fid
     * @return \think\response\Json
     */
    public function index($fid=0,$type=''){
        return parent::index($fid,$type);
    }
    
    /**
     * 首页幻灯片
     * @return \think\response\Json
     */
    public function banner(){
        return parent::banner();
    }
}













