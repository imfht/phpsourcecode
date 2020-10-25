<?php
/* 2016年12月29日 星期四 系统信息发布模块 */
namespace app\admin\controller;
use think\Controller;
class Info extends Controller
{
    public function _initialize(){
        if($this->_initTplCheck(['save'])) return;
        $action = request()->action();
        $option = [
            'auth'=>'','title'=>'Conero-系统信息发布','js'=>['Info/'.$action],'css'=>['Info/'.$action],'bootstrap'=>true
        ];
        switch($action){
            case 'edit':
                $option['require'] = 'tinymce';
                break;
        }
        $this->loadScript($option);
    }   
    // 首页 
    public function index()
    {
        // 数据表格 提取
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['title'=>'标题','type'=>'类型','descrip'=>'简介','state'=>'状态','remark'=>'备注','source'=>'来源','mtime'=>'创建时间','push_date'=>'编辑日期']]);
        $count = $wh? $this->croDb('sys_infor')->where($wh)->count() : $this->croDb('sys_infor')->count();        
        $bstp->tableGrid(['__viewTr__'=>'trs'],
            ['table'=>'sys_infor',
                'cols'=>[function($data){return '<a href="/conero/admin/info/edit.html?uid='.bsjson(['mtime'=>sysdate('date'),'no'=>$data['no']]).'">'.$data['title'].'</a>';},'type','descrip','state','remark','source','mtime','push_date']],
                function($db){
                    $bstp = $this->bootstrap();
                    $page = $bstp->page_decode();
                    $wh = $bstp->getSearchWhere();
                    return $db->page($page,30)->where($wh)->order('type')->select();
        });
        $bstp->pageBar($count);
        return $this->fetch();
    }
    // 编辑
    public function edit()
    {
        $uid = isset($_GET['uid'])? bsjson($_GET['uid']):array();
        $no = isset($uid['no'])? $uid['no']:'';
        $page = ['mode'=>'A'];
        if($no){
            $data = $this->croDb('sys_infor')->where('no',$no)->find();
            $data['hidden'] = '<input type="hidden" name="no" value="'.$no.'" readonly>';
            $this->assign('data',$data);
            $page['mode'] = 'M';
            $this->_JsVar('deleteurl','/conero/admin/info/save.html?uid='.bsjson(['no'=>$data['no'],'mode'=>'D']));
        }
        $sourceHelper = '';
        $data = $this->croDb('sys_infor')->where('source is not null')->field('source')->group('source')->select();
        foreach($data as $v){
            $sourceHelper .= '<option value="'.$v['source'].'">'.$v['source'].'</option>';
        }
        if($sourceHelper) $page['sourceSelect'] = '<select name="source" class="form-control">'.$sourceHelper.'</select>';
        $page['descrip'] = $no? '修改':'新增';        
        $this->assign('page',$page);
        return $this->fetch();
    }
    // 保存
    public function save()
    {
        $data = $_POST;
        $tb = 'sys_infor';
        if(isset($_GET['uid'])){
            $data = bsjson($_GET['uid']);
            if(isset($data['mode']) && isset($data['no']) && $data['mode'] == 'D'){
                $this->pushRptBack($tb,['no'=>$data['no']],true);
                if($this->croDb($tb)->where('no',$data['no'])->delete()) $this->success('数据已经被删除！','info/index');
                else $this->success('本次数据删除失败，十分抱歉！','info/index');
            }
            $this->success('非法操作，请终止！','info/index');
        }
        elseif(isset($data['no'])){     // 数据修改            
            //println($data);die;
            $no = $data['no'];unset($data['no']);
            if($this->croDb($tb)->where('no',$no)->update($data)) 
                $this->success('数据已经成功修改...!','info/index');
        }
        elseif($data){
            $uInfo = uInfo();      
            $data['user_code'] = $uInfo['code'];
            $data['name'] = $uInfo['name']; 
            if($this->croDb($tb)->insert($data)) 
                $this->success('数据新增成功!','info/index');
        }
        println($data);
    }
}				