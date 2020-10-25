<?php
/* 2016年11月23日 星期三 系统模板 */
namespace app\admin\controller;
use app\common\controller\BasePage;
use think\Db;
class Textpl extends BasePage
{
    // 首页
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统模板','bootstrap'=>true
        ]);
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $count = $this->croDb('sys_texttpl')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'名称','method'=>'机制','group'=>'分组','descript'=>'描述','remark'=>'备注','edittm'=>'编辑日期','editor'=>'作者']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'sys_texttpl','cols'=>[
            function($record){ return '<a href="'.url('textpl/edit','id='.$record['tpl_no']).'">'.(empty($record['name'])? '详情':$record['name']).'</a>';},
            'group','method','descript','remark','edittm','editor'],'dataid'=>'tpl_no'],function($db)use($wh){
            $page = $this->bootstrap()->page_decode();
            return $db->where($wh)->select();
        });
        $this->bootstrap($this->view)->pageBar($count);
        return $this->fetch();
    }
    // 编辑页面
    public function edit()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统模板-编辑','js'=>['Textpl/edit'],'bootstrap'=>true,'require'=>['tinymce']
        ]);
        $id = request()->param('id');
        if($id !== null){
            $tpl = model('Textpl');
            $data = $tpl->get($id)->toArray();
            $data['mode'] = 'M';
            $data['deleteUrl'] = urlBuild('!.textpl/save',['__get'=>['croCodeBs'=>bsjson(['mode'=>'D','tpl_no'=>$data['tpl_no']])]]);
            // println($data);
            $this->assign('data',$data);
        }
        return $this->fetch();
    }
    protected function _savedata(&$data){
        if(isset($data['mode']) && $data['mode'] == 'A'){
            $data['editor'] = uInfo('nick');
        }
        return [
            'table' => model('Textpl'),
            'pk'    => 'tpl_no'
            // ,'map'    => '条件'            
        ];
    }
}