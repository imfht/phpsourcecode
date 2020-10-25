<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase;
use app\common\traits\LabelhyEdit;


abstract class Labelhy extends IndexBase
{
    use LabelhyEdit;
    protected $tab_ext ;
    protected $form_items;
    protected $model;                 //内容模型
    protected $m_model;            //模块模型
    protected $s_model;              //栏目模型
    
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->check_power() !== true) {
            $this->error('你没权限!');
        }
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];        
        $this->model = get_model_class($dirname,'content');
        $this->m_model = get_model_class($dirname,'module');
        $this->s_model = get_model_class($dirname,'sort');
        //底部按钮
        $this->tab_ext = [
                'addbtn'=>'<a href="'.auto_url('delete',$this->get_parameter()).'"><button  type="button" class="btn btn-default">清空数据</button></a> 
                                    <a href="'.auto_url('index/labelhy/index',$this->get_parameter()).'?limit_type=system"><button  type="button" class="btn btn-default">更换其它</button></a> 
                                   <button onclick="parent.layer.close(parent.layer.getFrameIndex(window.name));parent.location.reload();" type="button" class="btn btn-danger">关闭当前窗口</button>',
                'hidebtn'=>'back',
        ];
        if(!in_wap()){  //非WAP端,强制使用PC模板
            define('USE_PC_TEMPLATE', true);
        }
    }
    
    
    /**
     * 通用标签设置
     * @return mixed|string
     */
    public function tag_set()
    {
        if($this->request->isPost()){
            //$this->request->post(['view_tpl'=>'']); //严禁模板代码
            $data = $this->request->post();
            if (empty($this->admin)) {
                foreach($data AS $value){
                    if (preg_match("/(<script |<iframe )/is", $value)) {
                        $this->error("禁止提交脚本与框架代码1");
                    }elseif( preg_match("/ (onload|onmouseover|onerror|onclick)([ \t\n\r]*)=/is", $value) ) {
                        $this->error("禁止提交脚本与框架代码2");
                    }
                }
            }
            $this->setTag_value("app\\".config('system_dirname')."\\model\\Content@labelGetList")
            ->setTag_type( $data['type'] );
            $_array = $this->get_post_data();
            //这里要做个权限判断,避免修改其它用户的信息 $_array['ext_id']
            $this->save($_array);
        }
        $url_array = $this->get_parameter();
        
        $info = $this->getTagInfo();        
        
        if(empty($info) || empty($info['view_tpl'])){
            //$info['view_tpl'] = $this->get_cache_tpl();
        }
        
        if ($info) {
            $rsdb = unserialize($info['cfg']);
        }else{
            $rsdb = cache('tag_default_'.input('name').input('hy_id').input('hy_tags'));
        }
        
        $mid = input('mid');
        
        //之前选定了辅栏目
        if (config('use_category')&&empty($mid)&&strstr($info['class_cfg'],'@labelGetCategoryList')) {
            header("location:".url('category_set',$url_array));
            exit;
        }
        
        if(empty($mid)&&!empty($rsdb['mid'])){
            $mid = $rsdb['mid'];
        }
        
        //模型分类菜单
        $nav = [];
        foreach ($this->m_model->getTitleList() AS $key=>$value){
            $nav[$key]=[
                    'title'=>$value,
                    'url'=>url('tag_set',array_merge($url_array,['mid'=>$key])),
            ];
        }
        
        if(config('use_category')&&category_config()){  //辅栏目存在的时候
            $nav['category']=[
                    'title'=>'辅栏目',
                    'url'=>url('category_set',$url_array),
            ];
        }
        
        if (empty($nav[$mid])) {    //考虑到更换频道后,有的模型并不存在
            unset($mid,$rsdb['mid']);
        }
        
        $mid || $mid=$this->m_model->getId();   //获取一个默认模型ID
        
        $this->tab_ext['nav'] =[
                $nav,
                $mid,
        ];
        
        if (input('hy_id')) {   //圈子使用
            $array = [
                ['hidden','mid',$mid],
                ['hidden','type',config('system_dirname')],
                //['radio','fidtype','栏目范围','',['不限','指定栏目'],0],
                //['checkboxtree','fids','指定栏目','不选择将显示所有栏目，要显示子栏目的话，必须全选中',$this->s_model->getTreeTitle(0,$mid,false)],
                ['radio','choose_type','条件筛选','',['uid'=>'所有我的','ext_id'=>'圈内成员的','uid-ext_id'=>'圈内我的'],'uid'],
                ['number','rows','显示条数','',5],
                ['number','leng','标题显示字数','',70],
                ['number','cleng','内容显示字数','',250],
                ['radio','ispic','是否要求有封面图','',['不限','必须要有封面图'],0],
                //['radio','status','范围限制','',['不限','已审','推荐'],0],
                ['radio','order','排序方式','',['id'=>'发布日期','view'=>'浏览量','list'=>'可控排序','rand()'=>'随机排序',]],
                ['radio','by','排序方式','',['desc'=>'降序','asc'=>'升序']],
                ];
        }else{  //全站使用
            $array = [
                ['hidden','mid',$mid],
                ['hidden','type',config('system_dirname')],
                ['radio','fidtype','栏目范围','',['不限','指定栏目','跟随栏目动态变化(仅适合列表页、内容页)'],0],
                ['checkboxtree','fids','指定栏目','不选择将显示所有栏目，要显示子栏目的话，必须全选中',$this->s_model->getTreeTitle(0,$mid,false)],
                ['number','rows','显示条数','',5],
                ['number','leng','标题显示字数','',70],
                ['number','cleng','内容显示字数','',250],
                ['radio','ispic','是否要求有封面图','',['不限','必须要有封面图'],0],
                ['radio','status','范围限制','',$this->get_status(),1],
                ['radio','order','排序方式','',['id'=>'发布日期','view'=>'浏览量','list'=>'可控排序','rand()'=>'随机排序',]],
                ['radio','by','排序方式','',['desc'=>'降序','asc'=>'升序']],
                ['radio','onlymy','指定用户数据','当前登录用户,适合在会员中心调用',['不限','当前登录用户','指定用户'],'0'],
                ['text','uids','指定用户uid','多个用户用半角逗号隔开'],
            ];
        }
        
        $self_form = $this->self_form();
        if ($self_form['form']) {
            if ($self_form['hide_model']) {
                unset($this->tab_ext['nav']);
            }
            if ($self_form['forbid_field']) {
                $detail = explode(',',$self_form['forbid_field']);
                foreach($array AS $k=>$v){
                    if(in_array($v[1], $detail)){
                        unset($array[$k]);
                    }
                }
            }
            if(count($self_form['form'])>5 || $self_form['form_title']){
                $this -> tab_ext['group'] = [                        
                    ($self_form['form_title']?:'个性设置')=>$self_form['form'],
                    '基础设置'=>$array,
                ];
                $array = [];
            }else{
                $array = array_merge($array,$self_form['form']);
            }            
        }
        $this->form_items = $array;
        
//         if($info['if_js']){ //APP站外调用,不使用模板,只要JSON数据
//             $num = count($this->form_items);
//             unset($this->form_items[$num-2] , $this->form_items[$num-1]);            
//         }
        
        $this->tab_ext['trigger'] = [
            ['fidtype', '1', 'fids'],
            ['onlymy', '2', 'uids'],
        ];
        
        $self_form['page_title'] && $this -> tab_ext['page_title'] = $array['page_title'];
        $self_form['help_msg'] && $this -> tab_ext['help_msg'] = $self_form['help_msg'];
        $self_form['trigger'] && $this -> tab_ext['trigger'] = array_merge($this -> tab_ext['trigger'],$self_form['trigger']);
        $self_form['template'] && $this -> tab_ext['template'] = $self_form['template'];
        
        return $this->editContent($rsdb);
    }
    
    
    protected function self_form(){
        $my_form_items = [];
        $data = input();
        $hy_id = isset($data['hy_id'])?$data['hy_id']:'';
        $hy_tags = isset($data['hy_tags'])?$data['hy_tags']:'';
        $name = $data['name'];
        $_array = cache('tag_default_'.$name.$hy_id.$hy_tags);
        if ($_array['conf'] && !strstr($_array['conf'],'/')) {
            $_array['conf'] = 'model_style/default/'.$_array['conf'];
        }
        if (empty($_array['conf'])) {
            return [];
        }
        $path = $_array['conf'].(strstr($_array['conf'],'.php')?'':'.php');
        if (is_file(TEMPLATE_PATH.'index_style/'.$path)) {
            $path = TEMPLATE_PATH.'index_style/'.$path;
        }else{
            $path = TEMPLATE_PATH.$path;
        }
        if (is_file($path)) {
            $array = include($path);
            if ( $array['form'] && is_array($array['form']) && $array['form'][0] ) {
                $my_form_items = $array;
            }
        }else{
            return [];
        }
        return $my_form_items;
    }
    
    
    /**
     * 类的标签设置
     * @return mixed|string
     */
    public function class_set(){
        
        if($this->request->isPost()){
            //str_replace('--', '\\', input('classname'))
            $this->setTag_value(mymd5(input('classname'),'DE'));
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        $info = $this->getTagInfo();
        
        if(empty($info) || empty($info['view_tpl'])){
            //$info['view_tpl'] = $this->get_listpage_cache_tpl();
        }
        
        $this->tab_ext['page_title'] = '类标签设置';
        
        $array = [
                //['hidden','div_width',input('div_width')],
                //['hidden','div_height',input('div_height')],
                ['hidden','type','class'],
                ['hidden','type','classname',input('classname')],
                ['number','rows','显示条数','',5],               
                //['radio','order','排序方式','',['id'=>'日期','rand()'=>'随机排序',],'id'],
                ['radio','by','排序方式','',['desc'=>'降序','asc'=>'升序'],'desc'],
                //['text', 'where', 'where查询条件(不懂PHP,禁止乱填,否则页面会报错)','例如:fid=5又或者fid|in|2,4,6@uid|not in|5,8'],                
                //['textarea','view_tpl','模板代码','',$info['view_tpl']],
//                 ['button', 'choose_style', [
//                         'title' => '点击选择模板',
//                         'icon' => 'fa fa-plus-circle',
//                         'href'=>url('index/label/choose_style',['type'=>'title','tpl_cache'=>'tags_page_demo_tpl_'.input('pagename'),'name'=>input('name')]),
//                         //'data-url'=>url('choose_style',['type'=>'images']),
//                         'class'=>'form-btn pop',
//                     ],
//                         'a'
//                 ],
        ];
        
        $self_form = $this->self_form();
        if ($self_form['form']) {
            if ($self_form['forbid_field']) {
                $detail = explode(',',$self_form['forbid_field']);
                foreach($array AS $k=>$v){
                    if(in_array($v[1], $detail)){
                        unset($array[$k]);
                    }
                }
            }
            $array = array_merge($array,$self_form['form']);
        }
        $this->form_items = $array;
        
        $self_form['page_title'] && $this -> tab_ext['page_title'] = $self_form['page_title'];
        $self_form['help_msg'] && $this -> tab_ext['help_msg'] = $self_form['help_msg'];
        $self_form['trigger'] && $this -> tab_ext['trigger'] = $self_form['trigger'];
        $self_form['template'] && $this -> tab_ext['template'] = $self_form['template'];

        return $this->editContent(unserialize($info['cfg']));
    }
    
}













