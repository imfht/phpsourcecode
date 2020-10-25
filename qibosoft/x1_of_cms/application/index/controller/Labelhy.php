<?php
namespace app\index\controller;

use app\common\traits\LabelhyEdit;

class Labelhy extends Label
{
    use LabelhyEdit;
    
    protected function _initialize()
    {
        parent::_initialize();
//         if ($this->check_power() !== true) {
//             $this->error('你没权限!');
//         }
        
        //底部按钮
        $this->tab_ext = [
                'addbtn'=>'<a href="'.auto_url('delete',$this->get_parameter()).'"><button  type="button" class="btn btn-default">清空数据</button></a> ',
                'hidebtn'=>'back',
        ];
//         if(!in_wap()){  //非WAP端,强制使用PC模板
//             define('USE_PC_TEMPLATE', true);
//         }
    }
    
    public function index()
    {
        $url_array = [
                'pagename'=>input('pagename'),
                'name'=>input('name'),
                'ifdata'=>input('ifdata'),
                'div_width'=>input('div_width'),
                'div_height'=>input('div_height'),
                'cache_time'=>input('cache_time'),
                'hy_id'=>input('hy_id'),
                'hy_tags'=>input('hy_tags'),
                'fromurl'=>urlencode(input('fromurl')),
        ];
        $type = input('type');
        if($type&&$type!='choose'){
            if($type=='image'){
                $url = url("index/labelhy/image",$url_array);                
            }elseif($type=='labelmodel'){
                $url = url("index/labelhy/labelmodel",$url_array);
            }elseif($type=='images'){
                $url = url("index/labelhy/images",$url_array);
            }elseif($type=='textarea'||$type=='text'||$type=='txt'){
                $url = url("index/labelhy/textarea",$url_array);
            }elseif($type=='ueditor'){
                $url = url("index/labelhy/ueditor",$url_array);
            }elseif($type=='member'){
                $url = url("index/labelhy/member",$url_array);
            }elseif($type=='link'){
                $url = url("index/labelhy/link",$url_array);
            }elseif($type=='links'){
                $url = url("index/labelhy/links",$url_array);
            }elseif($type=='myform'){
                $url = url("index/labelhy/myform",$url_array);
//             }elseif($type=='sql'){
//                 $url = url("index/labelhy/sql",$url_array);
            }elseif(modules_config($type)){
                $url = url("$type/labelhy/tag_set",$url_array);
            }elseif(plugins_config($type)){
                $url = purl("$type/labelhy/tag_set",$url_array);
            }elseif(preg_match('/^listpage_set_/', $type)){
                $name = str_replace('listpage_set_','',$type);
                $url = url("$name/labelhy/listpage_set",$url_array);
            }elseif(preg_match('/^showpage_set_/', $type)){
                $name = str_replace('showpage_set_','',$type);
                $url = url("$name/labelhy/showpage_set",$url_array);
            }elseif(preg_match('/^comment_set_/', $type)){
                $name = str_replace('comment_set_','',$type);
                $url = purl("comment/labelhy/set",$url_array);
            }elseif(preg_match('/^reply_set_/', $type)){
                $name = str_replace('reply_set_','',$type);
                $url = url("$name/labelhy/reply_set",$url_array);
           // }elseif(preg_match('/@/', $type)){
            }elseif(($_type=mymd5($type,'DE'))!=''){
                list($str,$action) = explode('@',$_type);
                list($m_p,$module,$dir,$file) = explode('\\',$str);//explode('--',$str);
                $classname = "\\$m_p\\$module\\index\\Labelhy";
                if (class_exists($classname)) {
                    $method = "{$file}_{$action}";
                    if (!method_exists($classname, $method)) {
                        $method = 'class_set';
                    }
                    $url_array['classname'] = $type;
                    if ($m_p=='app') {
                        $url = url("$module/labelhy/$method",$url_array);
                    }else{
                        $url = purl("$module/labelhy/$method",$url_array);
                    }
                }
            }
            if($url){
                echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=$url'>";
                exit;
            }
        }
        $module_db = $this->get_module($url_array,'labelhy');
        $plugin_db = $this->get_plugins($url_array,'labelhy');
        $this->assign('module_db',$module_db);
        $this->assign('plugin_db',$plugin_db);
        $this->assign('url',url('index',$url_array));
		return $this->fetch();
    }    
    
    //SQL高级查询调用
    public function sql($name=0,$pagename=0){
    }
    
    //会员调用
    public function member($name=0,$pagename=0){
        if(IS_POST){
            $data = $this -> request -> post();
            $this->setTag_value("app\\common\\model\\User@labelGet")
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $this -> tab_ext['page_title'] = '会员数据调用';
        
        $info = $this->getTagInfo();
        
        if(empty($info) || empty($info['view_tpl'])){
            //$_array = cache('tags_page_demo_tpl_'.$pagename);
            //$info['view_tpl'] = $_array[$name]['tpl'];
        }
        $this->tab_ext['hidebtn']='back';
        
        $cfg = unserialize($info['cfg']);
        $cfg['order'] || $cfg['order']='uid';
        $cfg['by'] || $cfg['by']='desc';
        $cfg['rows'] || $cfg['rows']=10;
        $form_items = [         
                ['number', 'rows', '取几条数据','',$cfg['rows']],
                ['checkbox', 'groupids', '显示哪些用户组','不选择,将显示所有',getGroupByid(),$cfg['groupids']],
                ['radio', 'order', '按什么排序','',['uid'=>'注册时间','lastvist'=>'最后访问时间','money'=>'积分数'],$cfg['order']],
                ['radio', 'by', '排序方式','',['desc'=>'降序','asc'=>'升序'],$cfg['by']], 
                ['text', 'where', 'where查询条件(不懂PHP,禁止乱填,否则页面会报错)','例如:fid=5又或者fid|in|2,4,6@uid|not in|5,8',$cfg['where']],
                ['text', 'whereor', 'whereOr查询条件(不懂PHP,禁止乱填,否则页面会报错)','例如:fid=5',$cfg['whereor']],
                ['textarea', 'view_tpl', '模板代码','',$info['view_tpl']],
                ['button', 'choose_style', [
                        'title' => '点击选择风格',
                        'icon' => 'fa fa-plus-circle',
                        'href'=>url('choose_style',['type'=>'title','tpl_cache'=>'tags_page_demo_tpl_'.input('pagename'),'name'=>input('name')]),
                        //'data-url'=>url('choose_style',['type'=>'images']),
                        'class'=>'form-btn pop',
                ],
                        'a'
                ],
        ];
        
        if($info['if_js']){ //APP站外调用,不使用模板,只要JSON数据
            $num = count($form_items);
            unset($form_items[$num-2] , $form_items[$num-1]);
        }
        
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    //单张图
    public function image(){
        if(IS_POST){
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetImage")
            ->setTag_extend_cfg(input('picurl').','.input('url').','.str_replace(',', '，', input('title')))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $array = $this->getTagInfo();
        $info = unserialize($array['cfg']);
        list($picurl,$url,$title) = explode(',',$array['extend_cfg']);
        $form_items = [
            ['image', 'picurl', '图片','',$picurl],
            ['text', 'url', '链接网址','',$url],
            ['text', 'title', '描述','',$title],
//                 ['number', 'pic_width', '图片宽度','',$cfg['pic_width']?$cfg['pic_width']:input('div_width')],
//                 ['number', 'pic_height', '图片高度','',$cfg['pic_height']?$cfg['pic_height']:input('div_height')],
        ];
        $this -> tab_ext['page_title'] = '单张图片';
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    

    /**
     * 组图
     */
    public function images($name=0,$pagename=0){        
        if(IS_POST){
            $data = $this -> request -> post();
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetImages")
            ->setTag_extend_cfg($data['extend_cfg'])
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $array = $this->getTagInfo();
        $info = unserialize($array['cfg']);
        $info['extend_cfg'] = $array['extend_cfg'];
        
        $this->tab_ext['hidebtn']='back';

        $cfg = unserialize($info['cfg']);
        $form_items = [
            ['images2', 'extend_cfg', '组图','',$info['extend_cfg']],
            ['number', 'pic_width', '图片宽度','有的风格可能设置无效',$cfg['pic_width']],
            ['number', 'pic_height', '图片高度','有的风格可能设置无效',$cfg['pic_height']],
        ];
        if($info['if_js']){ //APP站外调用,不使用模板,只要JSON数据
            $num = count($form_items);
            unset($form_items[$num-2] , $form_items[$num-1]);
        }
        $this -> tab_ext['page_title'] = '组图上传';
        $this->tab_ext['hidebtn']='back';        
        return $this -> get_form_table($info, $form_items);
    }
    
    /**
     * 百度在线编辑器
     */
    public function ueditor(){        
        if(IS_POST){            
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetUeditor")
            ->setTag_extend_cfg(input('extend_cfg'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $array = $this->getTagInfo();
        $info = unserialize($array['cfg']);
        if(empty($array) || empty($array['extend_cfg'])){
            $info['extend_cfg'] = $this->get_cache_tpl();
        }else{
            $info['extend_cfg'] = $array['extend_cfg'];
        }
        
        $this -> tab_ext['page_title'] = '在线编辑器';
        
        $form_items = [
                ['ueditor', 'extend_cfg','内容代码','',$info['extend_cfg']],
        ];
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    /**
     * 多行文本
     * {@inheritDoc}
     * @see \app\index\controller\Label::textarea()
     */
    public function textarea(){
        if(IS_POST){
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetTextarea")
            ->setTag_extend_cfg(input('extend_cfg'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        $this -> tab_ext['page_title'] = '纯文本';
        
        $array = $this->getTagInfo();
        $info = unserialize($array['cfg']);
        if(empty($array) || empty($array['extend_cfg'])){
            $info['extend_cfg'] = $this->get_cache_tpl();
        }else{
            $info['extend_cfg'] = $array['extend_cfg'];
        }
        $form_items = [
                ['textarea', 'extend_cfg','内容代码','',$info['extend_cfg']],
        ];
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
}
