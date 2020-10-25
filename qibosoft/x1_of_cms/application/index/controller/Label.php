<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use app\common\traits\LabelEdit;

class Label extends IndexBase
{
    use LabelEdit;
    protected $tab_ext;
    protected $form_items;
    
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->check_power() !== true) {
            $this->error('你没权限!');
        }
        
        //底部按钮
        $this->tab_ext = [
                'addbtn'=>'<a href="'.auto_url('delete',$this->get_parameter()).'"><button  type="button" class="btn btn-default">清空数据</button></a> ',
                'hidebtn'=>'back',
        ];
        if(!in_wap()){  //非WAP端,强制使用PC模板
            define('USE_PC_TEMPLATE', true);
        }
    }
    
    /**
     * 标签选择调用什么类型的数据
     * @return mixed|string
     */
    public function index()
    {
        $url_array = [
                'pagename'=>input('pagename'),
                'name'=>input('name'),
                'ifdata'=>input('ifdata'),
                'div_width'=>input('div_width'),
                'div_height'=>input('div_height'),
                'cache_time'=>input('cache_time'),
                'fromurl'=>urlencode(input('fromurl')),
        ];
        $type = input('type');
        if($type&&$type!='choose'){
            if($type=='image'){
                $url = url("index/label/image",$url_array);                
            }elseif($type=='labelmodel'){
                $url = url("index/label/labelmodel",$url_array);
            }elseif($type=='images'){
                $url = url("index/label/images",$url_array);
            }elseif($type=='textarea'||$type=='text'||$type=='txt'){
                $url = url("index/label/textarea",$url_array);
            }elseif($type=='ueditor'){
                $url = url("index/label/ueditor",$url_array);
            }elseif($type=='member'){
                $url = url("index/label/member",$url_array);
            }elseif($type=='link'){
                $url = url("index/label/link",$url_array);
            }elseif($type=='links'){
                $url = url("index/label/links",$url_array);
            }elseif($type=='myform'){
                $url = url("index/label/myform",$url_array);
            }elseif($type=='sql'){
                $url = url("index/label/sql",$url_array);
            }elseif(modules_config($type)){
                $url = url("$type/label/tag_set",$url_array);
            }elseif(plugins_config($type)){
                $url = purl("$type/label/tag_set",$url_array);
            }elseif(preg_match('/^listpage_set_/', $type)){
                $name = str_replace('listpage_set_','',$type);
                $url = url("$name/label/listpage_set",$url_array);
            }elseif(preg_match('/^showpage_set_/', $type)){
                $name = str_replace('showpage_set_','',$type);
                $url = url("$name/label/showpage_set",$url_array);
            }elseif(preg_match('/^comment_set_/', $type)){
                $name = str_replace('comment_set_','',$type);
                $url = purl("comment/label/set",$url_array);
            }elseif(preg_match('/^reply_set_/', $type)){
                $name = str_replace('reply_set_','',$type);
                $url = url("$name/label/reply_set",$url_array);
            //}elseif(preg_match('/@/', $type)){
            }elseif(($_type=mymd5($type,'DE'))!=''){
                list($str,$action) = explode('@',$_type);
                list($m_p,$module,$dir,$file) = explode('\\',$str);//explode('--',$str);
                $classname = "\\$m_p\\$module\\index\\Label";
                if (class_exists($classname)) {
                    $method = "{$file}_{$action}";
                    if (!method_exists($classname, $method)) {
                        $method = 'class_set';
                    }
                    $url_array['classname'] = $type;
                    if ($m_p=='app') {
                        $url = url("$module/label/$method",$url_array);
                    }else{
                        $url = purl("$module/label/$method",$url_array);
                    }
                }
            }
            if($url){
                echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=$url'>";
                exit;
            }
        }
        $module_db = $this->get_module($url_array);
        $plugin_db = $this->get_plugins($url_array);
        $this->assign('module_db',$module_db);
        $this->assign('plugin_db',$plugin_db);
        $this->assign('url',url('index',$url_array));
		return $this->fetch();
    }
    
    /**
     * 获取可以调用的频道
     * @param unknown $url_array
     * @return NULL[]|unknown[]|string[]|array[]|NULL[][]
     */
    protected function get_module($url_array,$type='label'){
        $array = [];
        $dir = opendir(APP_PATH);
        while (($file = readdir($dir))!==false) {
            if($file!='search'&&$file!='tongji'&&$file!='.'&&$file!='..'&&is_dir(APP_PATH.$file)&&modules_config($file)){
                if(is_file(APP_PATH."$file/index/".ucfirst($type).".php")){
                    $class = "\\app\\$file\\index\\Label";
                    if(class_exists($class)&&method_exists($class,'tag_set')){
                        $_ar = modules_config($file);
                        if($_ar){
                            $_ar['label_url'] = url("$file/$type/tag_set",$url_array);
                            $array[$file] = $_ar;
                        }
                    }                                        
                }
            }
        }
        return $array;
    }
    
    /**
     * 获取可以调用的插件
     * @param unknown $url_array
     * @return NULL[]|unknown[]|string[]|array[]|NULL[][]
     */
    protected function get_plugins($url_array,$type='label'){
        $array = [];
        $dir = opendir(PLUGINS_PATH);
        while (($file = readdir($dir))!==false) {
            if($file!='.'&&$file!='..'&&is_dir(PLUGINS_PATH.$file)&&plugins_config($file)){
                if(is_file(PLUGINS_PATH."$file/index/".ucfirst($type).".php")){
                    $class = "\\plugins\\$file\\index\\Label";
                    if(class_exists($class)&&method_exists($class,'tag_set')){
                        $_ar = plugins_config($file);
                        if($_ar){
                            $_ar['label_url'] = purl("$file/$type/tag_set",$url_array);
                            $array[$file] = $_ar;
                        }
                    }                    
                }
            }
        }
        return $array;
    }
    
    /**
     * 碎片模块管理
     * @return mixed|string
     */
    public function labelmodel(){
        
        $info = $this->getTagInfo();
        $cfg = unserialize($info['cfg']);
        
        $tag_name = input('name');
        $hy_id = input('hy_id');
        $cfg = cache('tag_default_'.$tag_name.$hy_id);
        
        $all_model = $this->get_common_model();
        $default_model = [];   //初始化的模块
        if (strstr($cfg['where'],'model=$')) {
            $default_model = explode(',', $cfg['model']);
        }else{
            $default_model = explode(',',str_replace('model=', '', $cfg['where']));
        }
        
        $_path = '';
        foreach($default_model AS $k=>$v){
            $v = trim($v," \r\n\t");
            if (empty($v)) {
                unset($default_model[$k]);
            }else{
                if(strstr($v,'/')){
                    $_path = dirname($v);
                }elseif(!strstr($v,'/') && $_path){
                    $v = $_path.'/'.$v;
                }
                $default_model[$k] = $v;
            }
        }
        
        $_array_path = [];
        foreach ($default_model AS $path){
            $path = str_replace('___', '/', $path);
            if (!$all_model[$path]) {
                $all_model[$path] = $path;
            }
            if(empty($_array_path) && !strstr($path,'model_style') && count(explode('/', $path))>2){
                if(is_file(TEMPLATE_PATH.'index_style/'.$path.'.'.config('template.view_suffix'))){
                    $_array_path = glob(TEMPLATE_PATH.'index_style/'.dirname($path).'/model_*.'.config('template.view_suffix'));
                }elseif(is_file(TEMPLATE_PATH.$path.'.'.config('template.view_suffix'))){
                    $_array_path = glob(TEMPLATE_PATH.dirname($path).'/model_*.'.config('template.view_suffix'));
                }
            }
        }
        
        //查找私有碎片的公共可选碎片 model_ 开头就列出来
        foreach($_array_path AS $path){
            //preg_match("/^<!--(.*?)-->/is", trim(file_get_contents($path)," \r\n\t"),$_ar);
            $path = str_replace([TEMPLATE_PATH,'.'.config('template.view_suffix')],'', $path);
            $all_model[$path] =  $path;
        }
        $all_model = $this->get_model_name($all_model);
        ksort($all_model);
        $use_ar = [];
        $use_model = json_decode($info['extend_cfg'],true)?:[];//最近设置的模块
        foreach($use_model AS $rs){
            $path =  str_replace('___', '/', $rs['path']);
            $use_ar[$path] = $path;
        }
        
        if(IS_POST){
            $data = $this->request->post();
            foreach($use_model AS $key=>$rs){
                $path = str_replace(  '___','/', $rs['path']);
                if(!in_array($path, $data['extend_cfg'])){
                    unset($use_model[$key]);
                }
            }
            
            foreach($data['extend_cfg'] AS $path){
                if (!in_array($path, $use_ar)) {
                    $use_model[] = [
                        'path'=>str_replace( '/', '___', $path),
                        'tags'=>$this->str2num($tag_name),
                    ];
                }
            }
            
            $this->setTag_value("app\\index\\controller\\Labelmodels@get_label")
            ->setTag_extend_cfg(json_encode($use_model))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        unset($info['extend_cfg']); //避免变量冲突
        $run_model = $info['id']?$use_ar:$default_model;    //在使用着的模块
        $form_items = [
            ['checkbox', 'extend_cfg', '已使用的模块','',$all_model,implode(',',$run_model)],
        ];
        $this -> tab_ext['page_title'] = '模块管理';
        
        $_all_model = [];
        foreach($all_model AS $path=>$name){            
            if(is_file(STATIC_PATH.$path.'.jpg')){
                $picurl = STATIC_URL.$path.'.jpg';
            }elseif(is_file(STATIC_PATH.$path.'.gif')){
                $picurl = STATIC_URL.$path.'.gif';
            }elseif(is_file(STATIC_PATH.$path.'.png')){
                $picurl = STATIC_URL.$path.'.png';
            }elseif(is_file(STATIC_PATH.$path.'.jpeg')){
                $picurl = STATIC_URL.$path.'.jpeg';
            }else{
                $picurl = '';
            }
            $_all_model[] = [
                'path'=>$path,
                'name'=>$name,
                'picurl'=>$picurl,
            ];
        }
        $this->assign('all_model',$_all_model);
        $this->assign('use_model',$run_model);
        $this->assign('recover_url',auto_url('delete',$this->get_parameter()));
        
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    /**
     * 把包含文件封闭起来,避免多个文件里边有变量冲突互相污染
     * @param string $file
     * @return unknown
     */
    private function get_file_cfg($file=''){
        defined('GET_CFG') || define('GET_CFG',true);
        $array = include($file);
        return $array;
    }
    
    /**
     * 获取公共碎片
     * @return unknown[]
     */
    protected function get_common_model(){
        $array = glob(TEMPLATE_PATH.'model_style/*/*.php');
        $data = [];
        $hyid = input('hy_id');
        $width = input('div_width');
        foreach ($array AS $file){
            $info = $this->get_file_cfg($file);
            if($info['type2']=='quote'){
                continue ;
            }
            if(IN_WAP===true){
                if($info['type1']=='pc'){
                    continue ;
                }
            }else{
                if($info['type1']=='wap'){
                    continue ;
                }
                if ($info['type3']=='small' && $width>500) {
                    continue ;
                }elseif ($info['type3']=='big' && $width<500) {
                    continue ;
                }
            }
            if ($hyid>0) {
                if($info['type2']=='www'){
                    continue ;
                }
            }else{
                if($info['type2']=='hy'){
                    continue ;
                }
            }
            if ($info['quote'] && $info['quote']!==true) {
                $detail = explode('|',$info['quote']);
                $ck = false;
                foreach(explode(',',$detail[0]) AS $v){
                    if (!$v) {
                        continue;
                    }
                    if (modules_config($v)||plugins_config($v)) {
                        $ck=true;
                        break;
                    }
                }
                if ($ck == false) {
                    continue ;
                }
            }
            $data[substr(strstr($file,'model_style/'),0,-4)] = $info['title'];
        }
        return $data;
    }
    
    /**
     * 获取风格模块名称
     * @param array $array
     */
    protected function get_model_name($array=[]){
        foreach($array AS $path=>$name){
            if(strstr($name,'/')){
                $file = TEMPLATE_PATH."index_style/{$path}.".config('template.view_suffix');
                if (!is_file($file)) {
                    $file = TEMPLATE_PATH."{$path}.".config('template.view_suffix');
                }
                //试图获取碎片名称,不存在就用路径显示
                preg_match("/^<!--(.*?)-->/is", trim(file_get_contents($file)," \r\n\t"),$data);
                $array[$path]=trim($data[1],'-')?:$path;
            }
        }
        return $array;
    }
    
    /**
     * 生成唯一固定数值
     * @param string $string
     * @return number
     */
    private function str2num($string=''){
        $j = 0;
        $num = strlen($string);
        for($i=0;$i<$num;$i++){
            $j +=ord(substr($string, $i , 1));
        }
        return 10000+$j;
    }
    
    
    /**
     * 自定义表单
     * @return mixed|string
     */
    public function myform($name='',$hy_id='',$hy_tags=''){
        if(IS_POST){
            $data = $this->request->post();
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetMyform")
            ->setTag_extend_cfg(json_encode($data))
            ->setTag_type( substr(strstr(__METHOD__,'::'),2) );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        $_array = cache('tag_default_'.$name.$hy_id.$hy_tags);

        $path = $_array['conf'].(strstr($_array['conf'],'.php')?'':'.php');
        if (is_file(TEMPLATE_PATH.'index_style/'.$path)) {
            $path = TEMPLATE_PATH.'index_style/'.$path;
        }else{
            $path = TEMPLATE_PATH.$path;
        }
        
        if (!is_file($path)) {
            $this->error($path.'配置文件不存在');
        }
        $array = include($path);
        if ( empty($array['form']) || !is_array($array['form']) || empty($array['form'][0]) ) {
            $this->error('表单参数不存在');
        }
        $form_items = $array['form'];
        
        $this -> tab_ext['page_title'] = $array['page_title']?:'自定义表单';
        $array['help_msg'] && $this -> tab_ext['help_msg'] = $array['help_msg'];
        $array['trigger'] && $this -> tab_ext['trigger'] = $array['trigger'];
        $array['template'] && $this -> tab_ext['template'] = $array['template'];
        
        $_info = $this->getTagInfo();
        $info = json_decode($_info['extend_cfg'],true);
        
        //         $form_items = [
        //                 ['textarea', 'extend_cfg','内容代码','',$info['extend_cfg']],
        //         ];
        $this->tab_ext['hidebtn'] = 'back';
        
        return $this -> get_form_table($info, $form_items);
    }
    
    /**
     * 标签设置 SQL原生查询语句
     * @param number $name
     * @param number $pagename
     * @return mixed|string
     */
    public function sql($name=0,$pagename=0){
        if(IS_POST){
            $data = $this -> request -> post();
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetSql")
            ->setTag_type('sql');
            $_array = $this->get_post_data();
            $this->save($_array);
        }

        $this -> tab_ext['page_title'] = '万能标签SQL原生查询数据调用';
        
        $info = $this->getTagInfo();
        
        
        if(empty($info) || empty($info['view_tpl'])){
            //$_array = cache('tags_page_demo_tpl_'.$pagename);
            //$info['view_tpl'] = $_array[$name]['tpl'];
        }
        $this->tab_ext['hidebtn']='back';
        
        $cfg = unserialize($info['cfg']);
        
        if(empty($cfg['sql'])){
            $_array = cache('tag_default_'.$name);
            $_array && $cfg['sql'] = $_array['sql'];
        }
        
        $form_items = [
                ['textarea', 'sql', '原生SQL查询语句','数据表前缀可以使用通配符代替：{pre}',$cfg['sql']],
                ['textarea', 'view_tpl', '模板代码','',$info['view_tpl']],
//                 ['button', 'choose_style', [
//                         'title' => '点击选择风格',
//                         'icon' => 'fa fa-plus-circle',
//                         'href'=>url('choose_style',['type'=>'title','tpl_cache'=>'tags_page_demo_tpl_'.input('pagename'),'name'=>input('name')]),
//                         //'data-url'=>url('choose_style',['type'=>'images']),
//                         'class'=>'form-btn pop',
//                 ],
//                         'a'
//                 ],
        ];
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    /**
     * 标签调用会员数据
     * @param number $name
     * @param number $pagename
     * @return mixed|string
     */
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
    
    /**
     * 标签添加单张图
     * @return mixed|string
     */
    public function image(){
        if(IS_POST){
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetImage")
            ->setTag_extend_cfg(input('picurl').','.input('url'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $info = $this->getTagInfo();
        $cfg = unserialize($info['cfg']);
        list($picurl,$title) = explode(',',$info['extend_cfg']);
        $form_items = [
                ['image', 'picurl', '图片','',$picurl], 
                ['text', 'url', '链接网址','',$title],
                ['number', 'pic_width', '图片宽度','',$cfg['pic_width']?$cfg['pic_width']:input('div_width')],
                ['number', 'pic_height', '图片高度','',$cfg['pic_height']?$cfg['pic_height']:input('div_height')],
        ];
        $this -> tab_ext['page_title'] = '单张图片';
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    
    /**
     * 标签设置单个菜单链接
     * @return mixed|string
     */
    public function Link(){
        if(IS_POST){
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetLink")
            ->setTag_extend_cfg(input('title')."\t".input('url')."\t".input('logo')."\t".input('font_color')."\t".input('bgcolor'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $info = $this->getTagInfo();
        $cfg = unserialize($info['cfg']);
        list($info['title'],$info['url'],$info['logo'],$info['font_color'],$info['bgcolor']) = explode("\t",$info['extend_cfg']);

        $form_items = [
            ['text', 'url', '链接网址'],
            ['text', 'title', '标题'],                                
            ['icon', 'logo', '图标'],
            ['color', 'font_color', '字体颜色'],
            ['color', 'bgcolor', '背景颜色'],
        ];
        $this -> tab_ext['page_title'] = '菜单链接';
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    /**
     * 标签设置多个菜单链接
     * @return mixed|string
     */
    public function Links(){
        if(IS_POST){
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetLinks")
            ->setTag_extend_cfg(input('extend_cfg'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $array = $this->getTagInfo();
        $info = unserialize($array['cfg']);
        $info['extend_cfg'] = $array['extend_cfg']; //赋值给表单
        $form_items = [
            ['links', 'extend_cfg', '导航链接'],
        ];
        $this -> tab_ext['page_title'] = '导航链接';
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    public function choose_style($type='title'){
        if($type==''){
            $this->error('类型有误！');
        }
        $tpl_cache = input('tpl_cache');
        $tag_name = input('name');
        if($tpl_cache && $tag_name){
            $cache_array = cache($tpl_cache);
            $cache_tpl_code = is_array($cache_array[$tag_name])?$cache_array[$tag_name]['tpl']:$cache_array[$tag_name];            
        }
        if (!empty(trim($cache_tpl_code))) {
            $listdb[]=[
                    'code'=>$cache_tpl_code,
                    'name'=>'默认模板',
                    'picurl'=>'/public/static/images/template/default.jpg'
            ];
        }
        $path = APP_PATH.'common/view/'.$type.'/';
        $array = get_dir_file($path,'htm');
        foreach($array AS $_file){
			$file = basename($_file);
            $name = substr($file, 0,-4);
            $listdb[]=[
                    'code'=>'<!--你当前选择的模板文件是：common/view/'.$type.'/'.$file.'-->'."\r\n".read_file($path.$file),
                    'name'=>$name,
                    'picurl'=>'/public/static/images/template/'.$type.'/'.$name.'/demo.jpg',
                    'title'=>$type.'/'.$file
            ];
        }

        $this->assign('listdb',$listdb);
        return $this->fetch();
    }
    
    //组图
    public function images($name=0,$pagename=0){        
        if(IS_POST){
            $data = $this -> request -> post();
//             $extend = json_encode(array_values($data['images2']['pics']));
//             unset($data['images2'],$data['pics']);
            $extend = $data['pics'];
            unset($data['pics']);
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetImages")
            ->setTag_extend_cfg($extend)
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $info = $this->getTagInfo();
        
        if(empty($info) || empty($info['view_tpl'])){
            //$_array = cache('tags_page_demo_tpl_'.$pagename);
            //$info['view_tpl'] = $_array[$name]['tpl'];
        }
        $this->tab_ext['hidebtn']='back';
        //$this->tab_ext['addbtn']='<a class="fa fa-plus-square pop" href="/"></a>';
//         $this->tab_ext['js_code']='  <script type="text/javascript">
// $("#choose_style").click(function(){
//   var url = $(this).data("url");
//   var layer_style =layer.open({
//             type: 2,
//              title: "风格选择",
//             area: ["90%", "90%"], 
//             content: url,
//          });
// });
//   </script>';
        
        $cfg = unserialize($info['cfg']);
        $form_items = [
                
                ['images2', 'pics', '组图','',$info['extend_cfg']],
                ['number', 'pic_width', '图片宽度','',$cfg['pic_width']?$cfg['pic_width']:input('div_width')],
                ['number', 'pic_height', '图片高度','',$cfg['pic_height']?$cfg['pic_height']:input('div_height')],
                ['textarea', 'view_tpl', '模板代码','',$info['view_tpl']],                
                ['button', 'choose_style', [
                        'title' => '点击选择风格',
                        'icon' => 'fa fa-plus-circle',
                        'href'=>url('choose_style',['type'=>'images','tpl_cache'=>'tags_page_demo_tpl_'.input('pagename'),'name'=>input('name')]),
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
        $this -> tab_ext['page_title'] = '组图上传';
        $this->tab_ext['hidebtn']='back';        
        return $this -> get_form_table($info, $form_items);
    }
    
    //在线编辑器
    public function ueditor(){        
        if(IS_POST){            
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetUeditor")
            ->setTag_extend_cfg(input('htmlcode'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        
        $info = $this->getTagInfo();
        
        if(empty($info) || empty($info['extend_cfg'])){
            $info['extend_cfg'] = $this->get_cache_tpl();
        }
        
        $this -> tab_ext['page_title'] = '在线编辑器';
        
        $form_items = [
                ['ueditor', 'htmlcode','内容代码','',$info['extend_cfg']],
        ];
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    protected function get_cache_tpl(){
        $_array = cache('tags_page_demo_tpl_'.input('pagename'));
        $_array && $code =trim($_array[input('name')]['tpl']); 
        return $code;
    }
    
    //多行文本
    public function textarea(){
        if(IS_POST){
            $this->setTag_value("app\\index\\controller\\LabelShow@labelGetTextarea")
            ->setTag_extend_cfg(input('htmlcode'))
            ->setTag_type(  substr(strstr(__METHOD__,'::'),2)  );
            $_array = $this->get_post_data();
            $this->save($_array);
        }
        $this -> tab_ext['page_title'] = '纯文本';
        
        $info = $this->getTagInfo();
        if(empty($info) || empty($info['extend_cfg'])){
            $info['extend_cfg'] = $this->get_cache_tpl();
        }
        $form_items = [
                ['textarea', 'htmlcode','内容代码','',$info['extend_cfg']],
        ];
        $this->tab_ext['hidebtn']='back';
        return $this -> get_form_table($info, $form_items);
    }
    
    
}
