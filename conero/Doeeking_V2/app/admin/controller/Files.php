<?php
/* 2016年11月23日 星期三 系统文件一览 */
namespace app\admin\controller;
use think\Controller;
class Files extends Controller
{
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-文件一览表','js'=>['User/index'],'css'=>['User/index'],'bootstrap'=>true
        ]);
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $count = $this->croDb('sys_file')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['file_name'=>'名称','url_name'=>'路径','file_type'=>'文件类型','file_size'=>'文件大小','edittm'=>'编辑时间']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'sys_file','cols'=>['file_name','url_name','file_type','file_size','edittm']],function($db)use($wh,$bstp){
                $page = $bstp->page_decode();
                return $db->page($page,30)->where($wh)->order('edittm desc')->select();
        });
        $this->bootstrap($this->view)->pageBar($count);
        return $this->fetch();
    }
}