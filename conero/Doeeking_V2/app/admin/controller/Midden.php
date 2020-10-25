<?php
/* 2017年2月7日 星期二 数据删除备份 */
namespace app\admin\controller;
use app\common\controller\BasePage;
class Midden extends BasePage
{
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-数据回收箱','bootstrap'=>true
        ]);      

        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $count = $this->croDb('sys_destory_bak')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['tbname'=>'数据表名','edittm'=>'编辑时间']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'sys_destory_bak','cols'=>[
            'tbname',
            function($record){
                $str =json_encode(bsjson($record['tbdata']));
                return '<a href="javascript:void(0)">'.(empty($record['tbdata'])? '数据无效':substr($str,0,10).'...'.substr($str,-10)).'</a>';
            }
        ,'edittm']],function($db)use($wh,$bstp){
            $page = $bstp->page_decode();
            return $db->where($wh)->page($page,30)->order('edittm desc,tbname')->select();
        });
        $this->bootstrap($this->view)->pageBar($count);

        return $this->fetch();
    }
}