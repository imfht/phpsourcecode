<?php
namespace app\geek\controller;
use think\Controller;
class Lang extends Controller
{
    public function _initialize()
    {
        if(request()->isAjax()) return;        
        $action = request()->action();
        if(!in_array($action,['save'])){
            geek_navBar($this->view,$this);
            $this->geekSpecilNavPlus();
        }
    }    
     // 定制导航下拉框
    protected function geekSpecilNavPlus()
    {
     
        $dropdown = '
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
            帮助我们 <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
            <li role="presentation"><a role="menuitem" tabindex="-1" href="/conero/geek/lang/edit.html">我来完善</a></li>                
            </ul>
        </li>
        ';
        $this->assign('geekSpecilNavPlus',$dropdown);
    }
    public function index()
    {
        $this->loadScript([
            'title'=>'Conero-计算机语言','bootstrap'=>true,'js'=>['Lang/index']
        ]);
        // 显示数据列表
        $bstp = $this->bootstrap($this->view);
        $wh = $bstp->getSearchWhere();
        $count = $this->croDb('gk_lang')->where($wh)->count();
        $bstp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['name'=>'名称','year'=>'年份','author'=>'作者','edittm'=>'编辑时间','editor'=>'贡献者']]);
        $bstp->tableGrid(['__viewTr__'=>'trs'],['table'=>'gk_lang','cols'=>[function($res){return '<a href="javascript:void(0);" class="about_lang_link">'.$res['name'].'</a>';},'year','author','edittm','editor']],function($db) use($bstp,$wh){
                $page = $bstp->page_decode();
                return $db->page($page,30)->where($wh)->select();
        });
        $bstp->pageBar($count);
        // 用户权限
        $uInfo = uInfo();
        $admin = isset($uInfo['admin'])? $uInfo['admin']:null;
        if($uInfo){
            $this->_JsVar('admin',$admin);
            $this->_JsVar('ulogin','Y');
        }
        return $this->fetch();
    }
    public function edit()
    {
        $this->loadScript([
            'title'=>'Conero-计算机语言','bootstrap'=>true,'require'=>['tinymce'],'js'=>['Lang/edit']
        ]);
        $pages = ['mode'=>'A'];
        $uid = isset($_GET['uid'])? bsjson($_GET['uid']):'';
        if($uid){
            if(isset($uid['mode'])) $pages['mode'] = $uid['mode'];
            $data = $this->croDb('gk_lang')->where('name',$uid['lang'])->find();
            $data['fkhtml'] = '<input type="hidden" name="listno" value="'.$data['listno'].'">';
            $this->assign('data',$data);
            // println($uid,$pages);
        }
        $this->assign('pages',$pages);
        return $this->fetch();
    }
    public function save()
    {
        $data = $_POST;
        if(isset($_GET['uid']) && count($data) < 1) $data = bsjson($_GET['uid']);
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        // 语言新增
        if($mode == 'A'){
            if(!empty($data['date'])){
                /*
                // 2
                $peg = '/^([\d]{4})+([\d-])+$/';
                if(preg_match($peg,$data['date'])){
                    $date = $data['date'];
                    if(substr_count($date,'-')){}
                }
                else unset($data['date']);
                */
                /*
                // 1
                try{
                    $date = date_create($data['date']);
                    $data['year'] = date_format($date, 'Y');
                    if(substr_count($data['date'],'-') != 2) unset($data['date']);
                }catch(Exception $e){
                    unset($data['date']);
                }
                */
                $date = trim($data['date']);
                $ctt = substr_count($date,'-');
                if($ctt == 0){
                    $data['year'] = $date;unset($data['date']);
                }                
            }
            $nick = uInfo('nick');            
            if($nick) $data['editor'] = uInfo('nick');            
            if($this->croDb('gk_lang')->insert($data)) $this->success('语言条目【新增】成功！',urlBuild('!geek:lang'));
        }
        // 语言修改
        elseif($data && 'M' == $mode){
            $map = ['listno'=>$data['listno']];
            unset($data['listno']);
            unset($data['mode']);
            if($this->croDb('gk_lang')->where($map)->update($data)) $this->success('语言条目【更新】成功！',urlBuild('!geek:lang'));
        }
        // 删除
        elseif($data && 'D' == $mode && 'DEV' == uInfo('admin')){
            if($this->croDb('gk_lang')->where('name',$data['lang'])->delete()) $this->success('语言条目【删除】成功！',urlBuild('!geek:lang'));
        }
        println($data);
    }
    public function ajax(){
        $item = isset($_POST['item'])? $_POST['item']:'';
        $data = $_POST;
        if($item) unset($data['item']);
        $ret = '';
        switch($item){
            case 'index/lang':
                $data = $this->croDb('gk_lang')->where('name',$data['lang'])->find();
                return $data;
                break;
        }
        return $ret;
    }
}
