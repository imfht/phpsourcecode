<?php
namespace app\index\controller;

use app\index\model\Labelhy AS LabelModel;

class LabelhyShow extends LabelShow
{
    public static $pri_hy_js=null;
    public static $labelhy_adminurl;
    
    protected function  _initialize()
    {
        parent::_initialize();
        if (defined('LABEL_SET') && LABEL_SET===true){
            set_cookie('labelhy_set','set');
        }
    }
    
    /**
     * 得到圈子黄页的ID,为的是给分页URL使用
     * @param number $id 圈子ID
     * @param string $tags 重复的标签编号
     * @return number
     */
    protected function get_hy_id($id=0,$tags=''){
        static $hy_id;
        static $hy_tags;
        if ($id||$tags) {
            $hy_id = $id;
            $hy_tags = $tags;
        }
//         if ($tags) {
//             $hy_tags = $tags;
//         }
        return [$hy_id,$hy_tags];
    }
    
    
    
    /**
     * 通用标签AJAX获取分页数据
     * @param string $tagname 标签变量名
     * @param string $page 第几页
     * @param string $pagename 标签所在哪个页面
     */    
    public function ajax_get($tagname='' , $page='' , $pagename='' , $hy_id=0 , $hy_tags=''){
        
        //对应fetch方法,传入一些常用的参数
        $admin = $this->admin;
        $userdb = $this->user;
        $timestamp = $this->timestamp;
        $webdb = $this->webdb;
        
        $parameter =get_post(); //这里不能用input 因为GET的优先级更高
        foreach ($parameter AS $key=>$value){
            if($value===''){
                unset($parameter[$key]);    //避免空值也执行where语句
            }else{
                //$value = urldecode(urldecode($value));
                //$value = urldecode($value);
                if( strstr($value,"'") ){
                    continue;
                }
                if (strstr($key,'?')) {
                    $parameter[$key] = $value;
                    $key = str_replace('?','',$key);
                    $this->request->get([$key=>$value]);
                    $$key=$value;
                }
                $parameter[$key] = $value;
            }
        }
        
        $_cfg = cache('tag_default_'.$tagname.$hy_id.$hy_tags);    //主要为的是传递where参数
        $parameter = array_merge($_cfg,$parameter);
        
        $live_cfg = self::union_live_parameter($parameter);
        
        $tag_array = LabelModel::get_tag_data_cfg($tagname , $pagename , $page, $live_cfg, $hy_id, $hy_tags);
        //$view_tpl = cache('tags_tpl_code_'.$tagname);      //原始模板缓存，非数据库的
        $_array = cache('tags_page_demo_tpl_'.$pagename);      //原始模板缓存，非数据库的
        if(!empty($_array)){
            $view_tpl = $_array[$tagname]['tpl'];
        }
        
        if(!empty($tag_array['view_tpl'])){         //数据库设定的模板优先
            $view_tpl = $tag_array['view_tpl'];
        }
        
        $__LIST__ = $tag_array['data'];
        $__array__ = $tag_array['pages'];       //分页数据
        
        
        if(empty($tag_array)){    //未入库前,随便给些演示数据
            $live_cfg && $_cfg = array_merge($_cfg,$live_cfg) ;
            $_cfg['sys_type'] && $_cfg['systype'] = $_cfg['sys_type'];      //重新定义了调取数据的类型, 也即动态变换
            $_cfg['tag_name'] = $tagname;
            $_cfg['page_name'] = $pagename;
            $__array__ = self::get_default_data($_cfg['systype']?$_cfg['systype']:'cms',$_cfg,$page,false);
            $__LIST__ = is_array($__array__['data']) ? $__array__['data'] : $__array__; //不是数组的时候,就是单张图片,或纯HTML代码
        }else{
            $_cfg = array_merge($_cfg,unserialize($tag_array['cfg'])) ;
        }
        
        //用户自定义了循环变量,比如listdb
        $val = $_cfg['val'];
        if(!empty($val)){
            $$val = $__LIST__;
        }
        
        if(empty($view_tpl)){
            //die('tpl not exists !');
            return $this->err_js('not_tpl');
        }
        
        if(empty($__LIST__)){
            //die('null');
            $content = '';
        }else{
            @ob_end_clean();ob_start();
            eval('?>'.$view_tpl);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $__array__['data'] = $content;
        return $this->ok_js($__array__);
    }
    
    
    /**
     * 生成通用标签的AJAX地址
     * @param array $array
     * @return mixed
     */
    protected function build_tag_ajax_url($array=[] , $type=''){
        $detail = $this->get_hy_id();
        $array['hy_id'] = $detail[0];   //不同于系统标签,这里必须要传递一下圈子黄页的ID
        $array['hy_tags'] = $array['tags'] = $detail[1];
        $array['sys_type'] = $this->get_sys_type();   //同一个标签,动态更换系统 type 参数
//         foreach($array AS $key=>$value){
//             $array[$key] = urlencode($value);
//         }
//         return iurl('index/labelhy_show/ajax_get',$array);

        $model_dir = 'index';
        if(!defined('IN_PLUGIN')){
            if ($type && !preg_match("/^([\w]+)$/", $type)) {
                preg_match("/app\\\([\w]+)\\\/",$type,$array);
                $type = $array[1];
            }
            $type = ($type&&modules_config($type))?$type:config('system_dirname');
            if ($type) {
                $path = APP_PATH.$type.'/index/LabelhyShow.php';
                $data = '<?php
namespace app\\'.$type.'\index;
use app\index\controller\LabelhyShow AS _LabelhyShow;
class LabelhyShow extends _LabelhyShow
{
}';
                if (is_file($path) || file_put_contents($path, $data)) {
                    $model_dir = $type;
                }
            }
        }
        
        return iurl($model_dir.'/labelhy_show/ajax_get').'?'.http_build_query($array).'&';
    }    
    
    
    /**
     * 解释标签
     * {@inheritDoc}
     * @see \app\index\controller\LabelShow::get_label()
     */
    public function get_label($tag_name='',$cfg=[]){
        $this->get_topic_quote($cfg);   //处理用户发表内容时,站内引用的主题
        $hy_id = $cfg['hy_id'];   //店铺ID
        $hy_tags = $cfg['hy_tags'];
        if (!is_numeric($hy_id)) {
            die('------店铺ID不存在------------');
        }
        if($cfg['systype']!='labelmodel' && $cfg['hy_id'] && strstr($cfg['union'],'uid') && !strstr($cfg['where'],'uid') && !strstr($cfg['where'],'ext_id')){
            $cfg['where'] = $cfg['where'] ? $cfg['where'].'&uid=$info.uid':'uid=$info.uid'; //针对圈子,自动加上uid查询
        }
        if($cfg['systype']=='labelmodel' && $cfg['Info'] && $cfg['Id']){    //缓存内容页的数据 Labelmodels.php 及ajax可能需要
            cache('tag_info-'.config('system_dirname').'-'.$cfg['Id'],$cfg['Info'],60);
        }
        $this->get_hy_id($hy_id,$hy_tags);
        $this->get_sys_type($cfg['sys_type']);
        $filtrate_field = $cfg['field'];                                 //循环字段指定不显示哪些
        $val = $cfg['val'];                                                 //取得数据后，赋值到这个变量名, 分页的话,没做处理会得不到
        $list = $cfg['list'];                                                //foreach输出 AS 后面的变量名
        $cfg['sys_type'] && $cfg['systype'] = $cfg['sys_type'];     //重新定义了调取数据的类型, 也即动态变换 type
        $type = $cfg['systype']?$cfg['systype']:'choose';            //选择哪种标签，图片或代码等等
        //         $pagename = md5( basename($cfg['dirname']) );       //模板目录名
        //if(empty($cfg['mid']))unset($cfg['mid']);       //避免影响到union那里动态调用mid
        if($cfg['mid']==-1){    // mid=-1 时 , 标志取所有模型的数据, 一般不建议这么做,效率非常低
            unset($cfg['mid']);
            $get_all_model = true;
        }else{
            $get_all_model = false;
        }
        static $pagename_array = [];    //避免重复执行
        $pagename = $pagename_array[$cfg['dirname']] = $pagename_array[$cfg['dirname']] ?: md5( $cfg['dirname'] );       //模板目录名
        $ifdata = intval($cfg['ifdata']);                            //是否只要原始数据
        
        static $filemtime_array = [];    //避免重复执行
        $filemtime = $filemtime_array[$pagename] = $filemtime_array[$pagename] ?: filemtime($cfg['dirname']);      //记录模板文件的修改时间，模板修改后，就取消缓存
        
        //某个页面所有标签的模板代码与演示数据
        static $page_demo_tpl_tags_array = []; //避免重复执行
        $page_demo_tpl_tags = $page_demo_tpl_tags_array[$pagename] = $page_demo_tpl_tags_array[$pagename] ?: cache('tags_page_demo_tpl_'.$pagename);
        $tpl_have_edit = false;
        if($filemtime!=$page_demo_tpl_tags['_filemtime_']){  //模板被修改过
            $tpl_have_edit = true;
        }
        
        echo self::pri_jsfile($pagename, $hy_id, $hy_tags );      //输出JS文件,要放在这个位置是因为其它函数可能要用到SHOW_SET_LABEL
        
        //$tag_array = cache('qb_tag_'.$tag_name.$hy_id.$hy_tags);        //取得具体某个标签的数据库配置参数，对于取文章列表的，也会同时得到相应的数据
        //if(empty($tag_array)||$tpl_have_edit){
        $tag_array = $type=='labelmodel' ? LabelModel::get_labelmodel_tag_data_cfg($tag_name , $pagename , 1 , self::union_live_parameter($cfg) , $hy_id  , $hy_tags , $cfg) : LabelModel::get_tag_data_cfg($tag_name , $pagename , 1 , self::union_live_parameter($cfg) , $hy_id  , $hy_tags  , $cfg);
            //$cache_time = isset($tag_array['cache_time'])?$tag_array['cache_time']:$cfg['cache_time'];
            //$cache_time>0 && cache('qb_tag_'.$tag_name.$hy_id.$hy_tags,$tag_array,$cache_time);
        //}
        
        if(!empty($tag_array) && !empty($tag_array['type'])){
            $type = $tag_array['type'];
        }
        $cfg['_type'] = $type;
        
        //$rows = $tag_array['rows']?$tag_array['rows']:$cfg['rows'];     //分页可能会用到
        
        if($tpl_have_edit){
            static $have_get_tpl_array = [];
            if(!$have_get_tpl_array[$pagename]){ //避免重复执行
                $have_get_tpl_array[$pagename] = true;
                $page_demo_tpl_tags = self::get_page_demo_tpl($cfg['dirname']);
                $page_demo_tpl_tags['_filemtime_'] = $filemtime;
                cache('tags_page_demo_tpl_'.$pagename,$page_demo_tpl_tags,36000);
            }
        }
        
        echo self::pri_tag_div($tag_name, $type, $tag_array, $cfg['class'], $hy_tags?iurl('index/labelhy/index',"pagename=$pagename&hy_id=$hy_id&hy_tags=$hy_tags"):'');    //输出标签的操作层
        
        if(empty($tag_array)){     //新标签还没有入库就输出演示数据
            
            if($type=='labelmodel'){    //自定义模块
                $cfg['class'] = "app\\index\\controller\\Labelmodels@get_label";
            }
            
            if($cfg['sql']){    //SQL原生查询语句
                $cfg['class'] = "app\\index\\controller\\LabelShow@labelGetSql";
            }
            //未入库前,标签默认指定的频道数据作为演示用
            if(empty($cfg['mid']) && !$get_all_model && !in_array('mid',explode(',',$cfg['union']))){
                $cfg['mid'] = 1; //指定模型效率会高点,但前提是模型1必须要存在,不然就会报错
            }
            if($type=='member'&&empty($cfg['class'])){
                $cfg['class'] = "app\\common\\model\\User@labelGet";
            }
//             if(    ($type&&( modules_config($type)||plugins_config($type) ))    ||    $cfg['class']    ){
//                 if($tpl_have_edit || empty( cache($hy_id.'tag_default_'.$tag_name) )){   //没入库前,也方便AJAX获取更多分页使用
//                     cache($hy_id.'tag_default_'.$tag_name,$cfg,3600);
//                 }
//             }
        }
        if($tpl_have_edit || empty( cache('tag_default_'.$tag_name.$hy_id.$hy_tags) )){   //方便AJAX使用
            cache('tag_default_'.$tag_name.$hy_id.$hy_tags,$cfg);
        }
        
        self::tag_cfg_parameter($tag_name,$cfg);  //把$cfg存放起来,给get_ajax_url使用
        
        //对应fetch方法,传入一些常用的参数
        $admin = $this->admin;
        $userdb = $this->user;
        $timestamp = $this->timestamp;
        $webdb = $this->webdb;
        
        //指定了过滤字段,代表想要取某些字段的数值,一般用在列表页,不适合聚合信息页多个频道混调
        $fields = ($filtrate_field && $cfg['mid']) ? $this->list_show_field( get_field($cfg['mid']) , $filtrate_field ) : [];
        
        if($cfg['js']){ //ajax显示数据,可以加快页面的打开速度
            $ajaxurl = $this->build_tag_ajax_url( array_merge(
                    [
                            'tagname'=>$tag_name,
                            'pagename'=>$pagename,
                            'page'=>1,
                    ],
                    self::union_live_parameter($cfg)
                ),$type?:$cfg['class']);
            print<<<EOT
<script type="text/javascript">
//对标签进行特殊处理
var code{$tag_name} = \$(".{$cfg['js']} .p8label");
code{$tag_name} = code{$tag_name}.length>0 ? code{$tag_name}.prop("outerHTML") : '';
\$(".{$cfg['js']}").html('内容加载中...');
\$(document).ready(function(){
	\$.get("{$ajaxurl}",function(res){
        if(res.code==0){
             \$(".{$cfg['js']}").html(code{$tag_name}+res.data);
        }else{
            layer.msg(res.msg,{time:500});
        }
       if(typeof({$cfg['js']})=='function'){ {$cfg['js']}(res); }
	});
               
});
</script>
EOT;
            return ;
        }
        
        if(empty($tag_array)){     //新标签还没有入库就输出演示数据
            
            if( ($type&&!in_array($type,['link','links'])&&( modules_config($type)||plugins_config($type) ))  ||  $cfg['class']){
                $cfg['tag_name'] = $tag_name;
                $cfg['page_name'] = $pagename;                
                $default_data = self::get_default_data($type,$cfg);
                if(!empty($val)){
                    $$val = $default_data;
                }else{
                    $__LIST__ = $default_data;
                }
                eval('?>'.$page_demo_tpl_tags[$tag_name]['tpl']);
                return ;
            }
            eval('?>'.$page_demo_tpl_tags[$tag_name]['demo']);
            
            return ;
            
        }else{
            //纯文本就直接输出
            if($type=='text'||$type=='txt'||$type=='textarea'||$type=='ueditor'){
                /*eval('?>'.$tag_array['data']);*/
                echo $tag_array['data'];
                return ;
            }elseif($type=='image'){    //单张图片,特别处理                
                $_tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                if(strstr($_tpl,'<?php')){	//单张图,有模板的情况
                    extract($tag_array['data']);
//                     $picurl = $tag_array['data']['picurl'];
//                     $url = $tag_array['data']['url'];
                    eval('?>'.$_tpl);
                }else{	//单张图,没有模板就直接输出图片
                    echo $tag_array['format_data'];
                }
                return $tag_array['data'];
            }elseif($type=='link'){     //菜单链接
                $_tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                extract($tag_array['data']);
//                 $url = $tag_array['data']['url'];
//                 $title = $tag_array['data']['title'];
//                 $logo = $tag_array['data']['logo'];
                eval('?>'.$_tpl);
                return $tag_array['data'];
            }elseif($type=='myform'){     //自定义表单
                $_tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                $_cfg = $tag_array['data'];
                $_cfg['id'] = $tag_array['id'];
                $__LIST__ = ['']; //为了循环有数据输出
                eval('?>'.$_tpl);
                return $tag_array['data'];
            }
            //针对图片处理
            $_cfg = unserialize($tag_array['cfg']);
            $_cfg['pic_width'] && $pic_width = $_cfg['pic_width'];
            $_cfg['pic_height'] && $pic_height = $_cfg['pic_height'];
            
            $__LIST__ = $tag_array['data'];
            if(!empty($val)){
                $$val = $__LIST__ ;
            }
            
            //什么都没有设置的时候，就直接输出
            if(empty($val)&&empty($page_demo_tpl_tags[$tag_name]['demo'])){
                $_tpl = trim(preg_replace('/<\?php(.*?)\?>/is','',$page_demo_tpl_tags[$tag_name]['tpl']));
                if(empty($_tpl) && empty($tag_array['view_tpl'])){
                    echo $tag_array['format_data']?$tag_array['format_data']:$tag_array['data'];
                    return ;
                }
            }
            if( $tag_array && trim($tag_array['view_tpl'])!='' ){         //数据库设定的模板优先
                $tpl = $tag_array['view_tpl'];
            }else{
                $tpl = $page_demo_tpl_tags[$tag_name]['tpl'];
                if($type=='images'&&trim(preg_replace('/<\?php (.*?)\?>/is','',$tpl))==''){	        //对于组图,没有默认模板的情况
                    echo $page_demo_tpl_tags[$tag_name]['demo'];
                    return ;
                }
            }
            eval('?>'.$tpl);
            return unserialize($tag_array['cfg']);   //显示更多分页可能会用到,比如可以判断数据少于rows的话,是否有需要显示更多按钮
        }
    }
    
    
    protected  function pri_jsfile($pagename='', $hy_id=0,$hy_tags=''){
        
        if(self::$pri_hy_js === null){
            self::$pri_hy_js = true;
            if (input('get.label_set')=='quit') {
                set_cookie('labelhy_set','');
            }
            if( input('get.labelhy_set')!='' ) {
                if(input('get.labelhy_set')=='quit' && defined('LABEL_SET') && LABEL_SET===true){ //同时退出全站的标签设置
                    set_cookie('label_set', input('get.label_set'));
                }
                set_cookie('labelhy_set',input('get.labelhy_set'));
            }
            
//             $this->weburl = str_replace(['labelhy_set=quit','labelhy_set=set','&&'], '', $this->weburl);
//             $weburl = strpos($this->weburl,'?') ? ($this->weburl.'&') : ($this->weburl.'?') ;

            if(input('get.labelhy_set')=='set' || get_cookie('labelhy_set')=='set'){
                $qun = fun("qun@getbyid",$hy_id);
                if($qun['uid']==$this->user['uid']  //圈主
                        || (defined('LABEL_SET') && LABEL_SET===true)   //管理员处于标签设置状态
                        ){
                    define('SHOW_SET_LABEL',true);      //Labelmodels.php 也用到此常量
                }else{  //不是圈主,也不是管理员就终止下面的标签相关显示
                    define('SHOW_SET_LABEL',false);
                    if ($this->admin && !$this->request->isAjax()) {
                        echo   $this->remind_set_label('goin');
                    }
                    return ;
                }
                
//                 if(in_wap()){
//                     $label_iframe_width = '95%';
//                     $label_iframe_height = '80%';
//                 }else{
//                     $label_iframe_width = '60%';
//                     $label_iframe_height = '80%';
//                 }
                
                $admin_url = iurl('index/labelhy/index',"pagename=$pagename&hy_id=$hy_id&hy_tags=$hy_tags");
                self::$labelhy_adminurl = $admin_url;     //Labelmodels.php要用到
                
                if ($this->request->isAjax()) {
                    return ;    //通过ajax获取数据的,就不要显示标签文件
                }
                
                if ($this->admin) {
                    echo   $this->remind_set_label('goout');
                }
                
                echo $this->get_label_jsfile($pagename);
//                 echo   "
//                <SCRIPT LANGUAGE='JavaScript' src='".STATIC_URL."libs/jquery-ui/jquery-ui.min.js'></SCRIPT>
//                 <SCRIPT LANGUAGE='JavaScript' src='".STATIC_URL."js/label.js'></SCRIPT>
//                 <SCRIPT LANGUAGE='JavaScript'>
//                 var admin_url='$admin_url',fromurl='/',label_iframe_width='$label_iframe_width',label_iframe_height='$label_iframe_height';
//                 </SCRIPT>";
            }elseif($this->admin && !$this->request->isAjax()){
                echo   $this->remind_set_label('goin');
            }
        }
    }
    
    /**
     * 标签上面显示的遮蔽层
     * @param string $tag_name
     * @param string $type
     * @param array $tag_array
     * @param string $class_name
     */
    protected  function pri_tag_div($tag_name='',$type='',$tag_array=[],$class_name='',$url=''){
        if(input('get.labelhy_set')=='set' || get_cookie('labelhy_set')=='set'){
            if (SHOW_SET_LABEL===false) {
                return ;
            }
            if (IS_TOPIC_QUOTE===true) {
                return ;    //站内引用主题不给设置标签
            }
            $tag_array['cfg'] = unserialize($tag_array['cfg']);
            $div_bgcolor = 'orange';
            $div_w = $tag_array['cfg']['div_width']>10?$tag_array['cfg']['div_width']:100;
            $div_h = $tag_array['cfg']['div_height']>10?$tag_array['cfg']['div_height']:30;
            if ($type=='labelmodel') {
                $div_h = $tag_array['cfg']['div_height']>30 ? 30 : $tag_array['cfg']['div_height'];
                $div_h<10 && $div_h = 30; 
                $div_bgcolor = '#06a0ce';
            }            
            if ($class_name!='' && ($type=='choose'||$type=='classname')) {
                $type = mymd5($class_name);//str_replace('\\', '--', $class_name);
            }
            
            echo "<style type='text/css'>.p8label{filter:alpha(opacity=50);position: absolute; border: 1px solid #ff0000; z-index: 9999; color: rgb(0, 0, 0); text-align: left; opacity: 0.4; width: {$div_w}px; height:{$div_h}px; background-color:$div_bgcolor;}
.p8label div{position: absolute; width: 15px; height: 15px; background: url(".STATIC_URL."js/se-resize.png) no-repeat scroll -8px -8px transparent; right: 0px; bottom: 0px; clear: both; cursor: se-resize; font-size: 1px; line-height: 0%;}</style>
<div class=\"p8label hylabel\" id=\"$tag_name\" onmouseover=\"showlabel_(this,'over','$type','');\" onmouseout=\"showlabel_(this,'out','$type','');\" onclick=\"showlabel_(this,'click','$type','$tag_name','".($url?:self::$labelhy_adminurl)."');\"><div onmouseover=\"ckjump_(0);\" onmouseout=\"ckjump_(1);\"></div></div>
            ";
        }        
    }
    
    
}
