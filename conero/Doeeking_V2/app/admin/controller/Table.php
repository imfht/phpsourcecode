<?php
/* 2016年11月23日 星期三 系统模板 */
namespace app\admin\controller;
use think\Controller;
use think\Db;
use hyang\Bootstrap;
class Table extends Controller
{
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统模板','js'=>['User/index'],'css'=>['User/index'],'bootstrap'=>true
        ]);        
        $bstp = (new Bootstrap())->linkApp($this->view);
        $wh = $bstp->getSearchWhere();
        $count = $this->croDb('table_view')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['TABLE_NAME'=>'名称','TABLE_ROWS'=>'数据量','AUTO_INCREMENT'=>'自增值','CREATE_TIME'=>'创建时间']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'table_view','cols'=>['TABLE_NAME','TABLE_ROWS','AUTO_INCREMENT','CREATE_TIME']],function($db){
                $bstp = $this->bootstrap();
                $page = $bstp->page_decode();
                $wh = $bstp->getSearchWhere();
                return $db->page($page,30)->where($wh)->select();
        });
        $this->bootstrap($this->view)->pageBar($count);
        return $this->fetch();
    }
}
