<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;
use app\index\model\Label AS LabelModel;

//内容页及列表页的母类
abstract class C extends IndexBase
{
    
    use ModuleContent;
    protected $model;                  //内容
    protected $mid;                    //模型ID
    protected $m_model;            //模块
    protected $f_model;              //字段
    protected $s_model;              //栏目
    protected $haibao;                //海报默认路径
    
    
    public function delete(){
        die('出错了!');
    }
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model        = get_model_class($dirname,'content');
        $this->s_model     = get_model_class($dirname,'sort');
        $this->m_model   = get_model_class($dirname,'module');
        $this->f_model     = get_model_class($dirname,'field');
        $this->haibao = TEMPLATE_PATH.'haibao_style/default/show.htm';           //海报默认路径
    }
    
    /**
     * 访问权限检查
     * 模块二次开发的时候,可以重写这个方法,进行更多的处理
     * @param array $info
     */
    protected function view_check(&$info=[]){
        if (empty(get_cookie('first_view'))) {
            set_cookie('first_view',$info['uid'].','.$info['ext_id']); //给定义为网站工具的圈子使用app\qun\index\Api那里用到;
        }
        //$info['hook_check'] 钩子可以对这个变量赋值,就可以绕过查看权限检查
        if(empty($info['status']) && empty($info['hook_check']) && !$this->admin && fun('admin@sort',$info['fid'])!==true && $this->user['uid']!=$info['uid']){
            $this->error('内容还没通过审核,你不能查看!');
        }
        $s_info = get_sort($info['fid'],'config');
        if ($s_info['allowview']) {
            if(empty($this->user) || (
                   // !$this->admin && 
                    !in_array($this->user['groupid'], explode(',',$s_info['allowview'])))   ){
                $this->error('你所在用户组无权查看!');
            }
        }
        
        $result = $this->market_check($info);
        if ($result!==true){
            $this->error($result);
        }
    }
    
    
    /**
     * 设置海报
     * @param array $info
     */
    protected function set_haibao($info=[]){
        $haibao = '';
        if ($info['haibao'] && is_file(TEMPLATE_PATH.'haibao_style/'.$info['haibao'])) {
            $haibao = TEMPLATE_PATH.'haibao_style/'.$info['haibao'];
        }
        if(empty($haibao)){
            $array = $info['fid'] ? get_sort($info['fid'],'config') : [];
            if($array['haibao']){
                list($default_file) = explode(',', $array['haibao']);
            }
            if ($default_file && is_file(TEMPLATE_PATH.'haibao_style/'.$default_file)) {
                $haibao = TEMPLATE_PATH.'haibao_style/'.$default_file;
            }
        }
        if(empty($haibao)){
            $array = model_config($info['mid']);
            if($array['haibao']){
                list($default_file) = explode(',', $array['haibao']);
            }
            if ($default_file && is_file(TEMPLATE_PATH.'haibao_style/'.$default_file)) {
                $haibao = TEMPLATE_PATH.'haibao_style/'.$default_file;
            }
        }
        if (empty($haibao)){
            //$haibao = 'content/haibao';
            $haibao = $this->haibao;
        }else{
            define('TPL_CACHE_PRE', basename(dirname($haibao)));    //要设置缓存的前缀,否则有缓存会不生效
        }
        $this->assign('haibao',$haibao);
    }
    
    /**
     * 列表页
     *          可以根据栏目ID或者模型ID，但不能为空，不然不知道调用什么字段
     * @param number $fid 栏目ID,可为空
     * @param number $mid 模型ID,不能为空
     * @return mixed|string
     */
    public function index($fid=0,$mid=0)
    {
        if(!$mid && !$fid){
            //$this->error('参数不存在！');
            $mid = 1;
        }elseif($fid){ //根据栏目选择发表内容
            $mid = $this->model->getMidByFid($fid);
            if(empty($mid)){
                $this->error('分类不存在!',404);
            }
        }
        
        $this->mid = $mid;
        $m_info = model_config($this->mid);        
        if(!$m_info){
            $this->error('模型不存在!',404);
        }
        
        
        //$field_array = get_field($this->mid);
        
        $data_list = [];
        //获取列表数据
        //         $data_list = $this->getListData($fid ? ['fid'=>$fid] : ['mid'=>$mid]);
        
        $s_info = $fid ? $this->sortInfo($fid) : [];
        
        //如果某个模型有个性模板的话，就不调用母模板
        $template = $this->get_tpl('list',$mid,$s_info);
        
        $GLOBALS['fid'] = $fid;     //标签有时会用到
        
        //列表显示哪些自定义字段
        //$tab_list = $this->getEasyIndexItems($field_array);
        
        //模板里要用到的变量值
        $vars = [
                //'listdb'=>$data_list,
                'fid'=>$fid,
                'mid'=>$mid,
                //    'pages'=>$data_list->render(),
                's_info'=>$s_info,
                'info'=>$s_info,
                'm_info'=>$m_info,
        ];
        $this->get_module_layout('list');   //重新定义布局模板
        return $this->fetch($template,$vars);
    }
    
    /**
     * 更新点击率
     * @param unknown $id
     */
    protected function updateView($id){
        $this->model->addView($id);
    }
    
    /**
     * 内容页
     * @param number $id 内容ID
     * @return mixed|string
     */
    public function show($id=0)
    {
        $this->mid = $this->model->getMidById($id);
        
        if(empty($this->mid)){
            $this->error('内容不存在!',404);
        }
        
        if ( empty($this->user) && in_weixin() && config('webdb.weixin_type')==3  ) {  //在微信端,就强制自动登录!
            weixin_login();
        }
        
        //获取内容数据
        $info = $this->getInfoData($id);
        
        $this->get_hook('cms_content_show',$info,$this->user);
        Hook_listen('cms_content_show',$info,$this->user);
        
        $this->view_check($info);   //访问权限检查
        
        $this->set_haibao($info);   //设置海报
        
        $this->updateView($id);     //更新浏览量
        
        //以下 picurl pics图库模型 是CMS模型,常用的几个字段,提前转义了          
//         if($info['picurl']){
//             $detail = explode(',',$info['picurl']);
//             $info['picurl'] = tempdir($detail[0]);
//             foreach($detail AS $key=>$value){
//                 $value && $info['picurls'][$key]['picurl'] = tempdir($value);
//             }
//         }

        $info['field_array'] = $this->get_field_fullurl($info);     //这行必须放在 format_field 的前面,这里要用到原始数据
        //$info = fun('field@format',$info,'','show');  //get_field_fullurl里边传了转义过的值,这里就不用再转义了
        
        //下面代码主要是避免 format_field 函数里边强行把picurl输出<img 这样的内容,导致无法对图片做个性显示
        if($info['field_array']['pics']['value']){  //CMS图库特别处理
            $info['picurls'] = $info['field_array']['pics']['value'];
            $info['picurl'] = $info['field_array']['pics']['value'][0]['picurl'];
        }else{
            $_picurl = $info['field_array']['picurl']['value'];
            if(is_array($_picurl)){
                $info['picurl'] = $_picurl[0]['picurl'];
                $info['picurls'] = $_picurl;
            }else{
                $info['picurl'] = $_picurl;
            }
        }
        
        
         $GLOBALS['fid'] = $info['fid'];     //标签有时会用到
        
        //栏目配置信息
        //$s_info = $this->sortInfo($info['fid']);
         $s_info = get_sort($info['fid'],'config')?:$this->sortInfo($info['fid']);
        
        //如果某个模型有个性模板的话，就不调用母模板
         $template = $this->get_tpl('show',$this->mid,$s_info,$info);
        
        //$field_db = $this->getEasyFormItems();     //自定义字段

        //模板里要用到的变量值
        $vars = [            
            'info'=>$info,
            'id'=>$id,
            'fid'=>$info['fid'],
            'mid'=>$info['mid'],
            'listdb'=>$info['picurls'],
            's_info'=>$s_info,
            'admin'=>$this->admin?:fun('admin@sort',$info['fid']),  //频道或栏目管理员的判断
        ];
        $this->get_module_layout('show');   //重新定义布局模板
        return $this->fetch($template,$vars);
    }
    
    /**
     * 列表页可能用到的筛选字段的处理,比如分类信息最常用的筛选字段
     * @param number $mid 模型ID
     * @return mixed[]
     */
    private function map_filter_field($mid=0){
        $data = get_post(); //不通知用input因为有优化级的处理
        $map = [];
        unset($data['fid'],$data['mid'],$data['page']);
        if(count($data)>0){
            //$farray = fun('field@list_filter',$mid);     //仅限于筛选字段
            $farray = get_field($mid);
            foreach($data AS $key=>$value){
                if($farray[$key]){   //判断字段是否存在,其实不判断也问题不大的.不过这里可以根据字段类型,扩展为别的字段查询,使用like语句
//                     if( in_array($farray[$key]['type'], ['radio','select']) ){
//                         $map[$key] = $value;
//                     }elseif($farray[$key]['type']=='checkbox'){
//                         $map[$key] = ['like',"%,$value,%"];
//                     }else{
//                         $map[$key] = ['like',"%$value%"];
//                     }         
                    $map[$key] = \app\common\field\Search::get_map($farray[$key]['type'],$value,$farray[$key]);
                }elseif(in_array($key, ['province_id','city_id','zone_id','street_id'])){
                    $map[$key] = $value;
                }
            }
        }
        return $map;
    }
    
    /**
     * 列表页通过标签显示的数据。 注意 并不包含分页采用ajax的情况
     * @param array $cfg
     * @return number|mixed|string|\think\Paginator
     */
    public function label_list_data($cfg = []){
//        $map = [];
        //筛选字段的处理,比如分类信息常用的
//         if(function_exists('get_filter_fields')){
            $map = $this->map_filter_field($cfg['mid']);
//         }
        
        if($cfg['status']>0){
            $map['status'] = ['>=',$cfg['status']];    //1是已审,2是推荐,已审要把推荐一起调用,所以要用>=
        }
        
        if($cfg['where']){  //用户自定义的查询语句
            $_array = fun('label@where',$cfg['where'],$cfg);
            if($_array){
                $map = array_merge($map,$_array);
            }
        }
//         $whereor = [];
//         if($cfg['whereor']){  //用户自定义的查询语句
//             $_array = fun('label@where',$cfg['whereor'],$cfg);
//             if($_array){
//                 $whereor = $_array;
//             }
//         }
        return $this->label_get_list_data($cfg['fid'],$cfg['mid'],$cfg['rows'],$cfg['order'],$cfg['by'],$map);
    }
    
    /**
     * 列表页从数据库取数据
     * @param number $fid 栏目ID
     * @param number $mid   模型ID
     * @param number $rows  每页几条
     * @param string $order 按什么方式排序
     * @param string $by    升序还是降序
     * @return array|\think\Paginator|number|mixed|string
     */
    private function label_get_list_data($fid=0,$mid=0,$rows=5,$order='list',$by='desc',$map=[]){
        $fid = intval($fid);
        if (!$fid && !$mid){
            $mid = current(model_config())['id'];   //考虑给其它非列表页调用的时候,不存在fid 也不存在 mid
            if(empty($mid)){
                return [];
            }            
        }        
        $by = $by == 'asc' ? 'asc' : 'desc';
        if($fid){
            $fids = get_sort($fid,'sons') ;
            $map['fid'] = $fids ? ['in',$fids] : $fid;
            $mid = get_sort($fid,'mid');    //$this->model->getMidByFid($fid);
        }
        $this->mid = $mid;  //getListData要用到的
        $rows = intval( $rows<1 ? 10 : $rows);
        $map['mid'] = $mid;
        $order = $order&&$order!='undefined' ? filtrate($order) : 'list';
//         if (!in_array($order, ['id','create_time','list','rand()','view'])) {
//             if(empty($order) || table_field($this->model->getTableByMid($mid),$order)==false){
//                 $order = 'list';
//             }
//         }
        $listdb = $this->getListData($map, "$order $by",  $rows , [] ,true);
        $listdb->each(function($rs,$key){
            if( $rs['status']==0 && (empty($this->user)||($rs['uid']!=$this->user['uid']&&fun('admin@sort',$rs['fid'])!==true)) ){
                return [];
            }else{
                if ($rs['fid'] && $allow_viewtitle=sort_config()[$rs['fid']]['allow_viewtitle']) {    //允许查看标题的用户组
                    if (empty($this->user)||!in_array($this->user['groupid'], explode(',',$allow_viewtitle))) {
                        return [];
                    }
                }
                return $rs;
            }
        });
        return $listdb;
    }
    
    
    /**
     * APP或小程序 方式JSON调用数据  获取列表页的分页数据
     * @param string $name 标签名
     * @param string $page  页码数，第几页
     * @param number $mid 模型ID
     * @param number $fid 栏目ID
     * @param number $rows 取几条数据
     * @param string $order 按什么方法排序
     * @param string $by    升序还是倒序
     * @param string $type 数据查找条件
     * @param string $where 多项组合查找条件
     * @param string $status 字段查找条件
     */
    public function app_get($name='',$page='',$mid=0, $fid=0 ,$rows=0,$order='',$by='',$type='yz',$where='',$status=''){        
        
        $array = cache('config_app_tags');
        if(empty($array)){
            $array = LabelModel::where(['if_js'=>1])->column(true,'name');
            cache('config_app_tags',$array);
        }
        
        $tag_array = $array[$name];
        if($tag_array){
            $cfg = unserialize($tag_array['cfg']);
            extract($cfg);
        }else{
            if(empty($this->webdb['open_app_get'])){
                return $this->err_js('为安全考虑,系统默认没有开放随意调用数据功能,要启用,请在开发者中心添加参数open_app_get为1');
            }
            //这里需要对外面传进来的各项参数做一个过滤判断 
        }
        
        $this->mid = $mid;
        
        $map = [];
        if($status>0){
            $map = [
                    'status'=>['>=',$status],    //1是已审,2是推荐,已审要把推荐一起调用,所以要用>=
            ];
        }
        if($type=='good'){  //取精华数据 这里容易跟上面的条件造成冲突,要注意
            $map = ['status'=>2];
        }
        
        if($where){  //用户自定义的查询语句
            $_array = fun('label@where',$where,$cfg);
            if($_array){
                $map = array_merge($map,$_array);
            }
        }
        //         $whereor = [];
        //         if($cfg['whereor']){  //用户自定义的查询语句
        //             $_array = fun('label@where',$cfg['whereor'],$cfg);
        //             if($_array){
        //                 $whereor = $_array;
        //             }
        //         }
        
        $data_list = $this->label_get_list_data($fid,$mid,$rows,$order,$by,$map);
        $data_list->each(function($rs,$key){
            unset($rs['content'],$rs['full_content']);
            return $rs;
        });
        $data_list = getArray($data_list);
        return $this->ok_js($data_list);
    }
    
    /**
     * AJAX 方式调用 无刷新获取列表页的分页数据
     * @param string $name 标签名
     * @param string $page  页码数，第几页
     * @param string $pagename 模板文件名
     * @param number $mid 模型ID
     * @param number $fid 栏目ID
     * @param number $rows 取几条数据
     * @param string $order 按什么方法排序
     * @param string $by    升序还是倒序
     * @param string $type 数据查找条件
     * @param string $data_type 取什么类型的数据，比如可以设置为json
     */
    public function ajax_get($name='',$page='' ,$pagename='',$mid=0, $fid=0 ,$rows=0,$order='',$by='',$type='yz',$where=''){
        
        //GET优先级比route高,方便重新再定义参数
        $getData = input('get.');
        $getData['mid'] && $mid = $getData['mid'];
        $getData['fid'] && $fid = $getData['fid'];
        $getData['rows'] && $rows = $getData['rows'];
        $getData['order'] && $order = $getData['order'];
        $getData['by'] && $by = $getData['by'];
        //这里需要对外面传进来的各项参数做一个过滤判断 
        
        if($fid<1){unset($fid);}
        if($getData['fid']<1){unset($getData['fid']);}
        $data_type = input('data_type'); 
        $this->mid = $mid;

        $page_tpl = cache('tags_listpage_tpl_'.$pagename);  //多个标签的模板缓存
        if(!empty($page_tpl)){
            $view_tpl = $page_tpl[$name];
        }
        
        $tag_array = cache('qb_tag_'.$name);    //数据库参数配置文件
        
        if(empty($tag_array)){                             //数据库设定的模板优先
            $tag_array = LabelModel::get_tag_data_cfg($name , $pagename);
            //cache('qb_tag_'.$tag_name,$tag_array,$tag_array['cache_time']);
            trim($tag_array['view_tpl']) && $view_tpl = $tag_array['view_tpl'];
        }
        if(empty($view_tpl)){
            return $this->err_js('not_tpl');
            //die('tpl not exists !');
        }
        $cfg = unserialize($tag_array['cfg']);  //保存在数据库,用户特别设置的
        $map = [];
        if($cfg['status']>0){
            $map = [
                    'status'=>['>=',$cfg['status']],    //1是已审,2是推荐,已审要把推荐一起调用,所以要用>=
            ];
        }elseif (input('status')!==null){
            $map = [
                    'status'=>['>=',input('status')],    //1是已审,2是推荐,已审要把推荐一起调用,所以要用>=
            ];
        }
        if($type=='good'){  //取精华数据 这里容易跟上面的条件造成冲突,要注意
            $map = ['status'=>2];
        }
        $filter_array = $this->map_filter_field($mid);   //列表页的选择字段
        if ($filter_array) {
            $map = array_merge($map,$filter_array);
        }
        
        if($cfg['where']){  ///保存在数据库,用户自定义的查询语句
            $_array = fun('label@where',$cfg['where'],$cfg);
            if($_array){
                $map = array_merge($map,$_array?:[]);
            }
        }elseif($where=mymd5($where,'DE')){ //URL中的where语句解密处理,避免用户恶意修改
            $_array = fun('label@where',$where,array_merge(input('route.'),$getData));
            $map = array_merge($map,$_array?:[]);
        }
        
//         $whereor = [];
//         if($cfg['whereor']){  //用户自定义的查询语句
//             $_array = fun('label@where',$cfg['whereor'],$cfg);
//             if($_array){
//                 $whereor = $_array;
//             }
//         }
        $data_list = $this->label_get_list_data($fid,$mid,$rows,$order,$by,$map);
        $array = getArray($data_list);
        $__LIST__ = $array['data'];
        foreach ($__LIST__ AS $key=>$value){
            if (empty($value)) {
                unset($__LIST__[$key]);
            }
        }
        $__LIST__ = array_values($__LIST__);
        if(empty($__LIST__)){
            //return $this->err_js('null');
            //die('null');
            $content = '';
        }else{            
            $val = $cfg['val'];
            if(!empty($val)){
                $$val = $__LIST__;
            }
            @ob_end_clean();ob_start();
            eval('?>'.$view_tpl);
            $content = ob_get_contents();
            ob_end_clean();
        }

        $array['data'] = $content;
        return $this->ok_js($array);
    }
    
    /**
     * 检查对应的模板是否存在,不存在就返回空值
     * @param unknown $type list 或 show 页
     * @return string
     */
    protected function check_file($filename){
        static $path;
		//static $default_path = null;
        if(empty($path[config('template.index_style')])){
            $path[config('template.index_style')] = dirname( makeTemplate('show',false) ).'/';
        }
        $file = $path[config('template.index_style')] . $filename . '.' . ltrim(config('template.view_suffix'), '.');
		if(is_file($file)){
           return $file;
        }
        
        //寻找default默认风格的模板路径
// 		if($default_path === null){
// 			if(config('template.default_view_path')!=''){
// 			    $view_path = config('template.view_path');
// 			    config('template.view_path',config('template.default_view_path'));
// 			    $default_path = dirname( makeTemplate('show',false) ).'/';
// 			    config('template.view_path',$view_path);
// 			}
// 		}		
// 		if($default_path!==null){
// 		    $file = $default_path . $type . '.' . ltrim(config('template.view_suffix'), '.');
// 		    if(is_file($file)){
// 		        return $file;
// 		    }
// 		}
    }
    
    
    /**
     * 获取频道设置的列表页或者是内容风格,但优先级比栏目指定的模板要低
     * @param string $type
     * @return string
     */
    protected function get_module_tpl($type='show'){
        if(IN_WAP===true){
            if( $this->webdb['module_wap_'.$type.'_template']!='' ){
                $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_wap_'.$type.'_template'];
            }
        }else{
            if( $this->webdb['module_pc_'.$type.'_template']!='' ){
                $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_pc_'.$type.'_template'];
            }
        }
        if(is_file($template)){
            return $template;
        }
    }
    
    /**
     * 挑选模板 不存在就返回空值
     * @param string $type 值为 list 或 show  
     * @param number $mid 模型ID
     * @param array $sort 栏目配置参数,比如设置了栏目模板
     * @param array $info 内容里边可能有定义了模板
     * @return string
     */
    protected function get_tpl($type='show',$mid=0,$sort=[],$info=[]){
        $template = '';
        
        //栏目若自定义了列表及内容页的模板，优先级是最高的
        if($sort['template']){
            $ar = unserialize($sort['template']);
            if(IN_WAP===true){
                if($type=='list' && $ar['waplist']){
                    $template = TEMPLATE_PATH.'index_style/'.$ar['waplist'];
                }elseif($type=='show' && $ar['wapshow']){
                    $template = TEMPLATE_PATH.'index_style/'.$ar['wapshow'];
                }
            }else{
                if($type=='list' && $ar['pclist']){
                    $template = TEMPLATE_PATH.'index_style/'.$ar['pclist'];
                }elseif($type=='show' && $ar['pcshow']){
                    $template = TEMPLATE_PATH.'index_style/'.$ar['pcshow'];
                }
            }
            if(!is_file($template)){
                $template = '';
            }
        }
        
        if (IN_WAP===true && $info['wap_template'] && is_file(TEMPLATE_PATH.'index_style/'.$info['wap_template']) && preg_match("/".config('template.view_suffix')."$/", $info['wap_template'])) {
            $template = TEMPLATE_PATH.'index_style/'.$info['wap_template'];
        }elseif (IN_WAP!==true && $info['pc_template'] && is_file(TEMPLATE_PATH.'index_style/'.$info['pc_template']) && preg_match("/".config('template.view_suffix')."$/", $info['pc_template'])) {
            $template = TEMPLATE_PATH.'index_style/'.$info['pc_template'];
        }
        
        
        //频道特别设置了列表或内容页模板,
        //重复提醒!!!!!!!!!!!!!一般只推荐只有一个模型的情况做设置
        //如果有多个模型的话,都会统一用这个模板,会导致不同的模型的自定义字段不好体现,所以多模型的时候,不建议设置
        if (empty($template)) {
            $template = $this->get_module_tpl($type);
        }
        
        //某个频道特别选择了某个开发者的整套频道风格 ,这会涉及到头部整体风格都会换掉
        //重要提醒!!!!!!!!! 这里跟上面这一项容易搞混,这里指的是可以整个频道使用某个开发者的风格,而不必再具体指定列表页或内容页及主页
        //上面是需要特别指定列表页或内容页或主页,这里就不用一一指定,而是全调用开发者的某套风格的这三种页面
        if (empty($template)) {
            //模型2以上的值,如果个性风格比如pc_show2.htm,show2.htm不存在,就取default默认风格的此文件.模型1的话,就仍用个性风格的pc_show.htm show.htm
            //$template = $this->get_module_layout_tpl($type,$mid,$mid==1?true:false);
            $template = $this->get_module_layout_tpl($type,$mid);
        }
        
        //网站系统的当前风格
        if (empty($template)) {
            //模型2以上的值,如果个性风格比如pc_show2.htm,show2.htm不存在,就取default默认风格的此文件.模型1的话,就仍用个性风格的pc_show.htm show.htm
            //$template = $this->get_auto_tpl($type,$mid,$mid==1?true:false);
            $template = $this->get_auto_tpl($type,$mid);
        }
        
        //新风格找不到的话,就寻找默认default模板,如果系统风格本来就是default默认风格的话,下面的不会执行
        if (empty($template)) {
            $template = $this->get_default_tpl($type,$mid);
        }
        
        //如果上面get_auto_tpl此函数设置了false参数,主要是针对模型2以上的情况
        //如果设置了false参数,系统本来就是默认风格的话,会导致上面的get_default_tpl不会执行,会导致  $template 为空,所以这里重新设法获取默模板
        if (empty($template)) {
            $template = $this->get_auto_tpl($type,'');
        }
        
        return $template;
    }
    
    /**
     * 获取default 默认目录的风格文件
     * @param string $type
     * @param number $mid
     * @return string
     */
    protected function get_default_tpl($type='show',$mid=0){
        if(config('template.view_base')){
            if( config('template.default_view_base') ){ //没有使用默认风格
                $view_base = config('template.view_base');
                $style = config('template.index_style');
                config('template.view_base',config('template.default_view_base'));
                config('template.index_style','default');   // check_file 此方法要用到
                $template = $this->get_auto_tpl($type,$mid);
                config('template.view_base',$view_base);
                config('template.index_style',$style);
            }
        }else{
            if(config('template.default_view_path')!=''){
                $view_path = config('template.view_path');
                $style = config('template.index_style');
                config('template.view_path',config('template.default_view_path'));
                config('template.index_style','default');
                $template = $this->get_auto_tpl($type,$mid);
                config('template.view_path',$view_path);
                config('template.index_style',$style);
            }
        }
        return $template;
    }
    
    
    /**
     * 频道特别选择了某个开发者的风格,而不必再具体指定列表页或内容页及主页 ,这会涉及到头部整体风格都会换掉
     * 按优先级寻找模板 比如优先级序顺是 wap_show2(pc_show2) 最高,其次是 show2 接着是 wap_show(pc_show) 最后是 show
     * @param string $type 可以为show 或 list
     * @param number $mid 模型ID
     * @param string $use_default 当前风格找不到模型参数模板时,是否使用不带模型参数的母模板
     * @return string
     */
    protected function get_module_layout_tpl($type='show',$mid=0,$use_default=true){
        //模型的模板优先级高于母模板
        if(IN_WAP===true){
            $template = $this->check_module_layout_file('wap_'.$type.$mid);
        }else{
            $template = $this->check_module_layout_file('pc_'.$type.$mid);
        }
        if(empty($template)){
            $template = $this->check_module_layout_file($type.$mid);
        }
        
        $model_cfg = model_config($mid);
        if(empty($template) && $model_cfg['keyword']){  //模型设置了关键字的情况,可以使用指定的模板,但优先级比模型ID的低
            if(IN_WAP===true){
                $template = $this->check_module_layout_file('wap_'.$type.'-'.$model_cfg['keyword']);
            }else{
                $template = $this->check_module_layout_file('pc_'.$type.'-'.$model_cfg['keyword']);
            }
            if(empty($template)){
                $template = $this->check_module_layout_file($type.'-'.$model_cfg['keyword']);
            }
        }
        
        //母模板
        if(empty($template) && $use_default==true){
            if(IN_WAP===true){
                $template = $this->check_module_layout_file('wap_'.$type);
            }else{
                $template = $this->check_module_layout_file('pc_'.$type);
            }
        }
        if(empty($template) && $use_default==true){
            $template = $this->check_module_layout_file($type);
        }
        return $template;
    }
    
    /**
     * 频道选择了某个开发者的风格,寻找此开发者的对应模板
     * @param unknown $filename 可以是index list show这三种页面的模板
     * @return string
     */
    protected function check_module_layout_file($filename='show'){
        $layout = $this->get_module_layout('default');  //获得某个开发者的风格布局模板详细路径,方便得出其所在的目录给下面使用
        if ($layout){
            $base_path = dirname(dirname($layout)) . '/' . config('system_dirname') . '/content/';
            $tpl_name = $filename . '.' . config('template.view_suffix');
            if(is_file($base_path.$tpl_name)){
                return $base_path.$tpl_name;
            }
        }
    }
    
    /**
     * 按优先级寻找模板 比如优先级序顺是 wap_show2(pc_show2) 最高,其次是 show2 接着是 wap_show(pc_show) 最后是 show
     * @param string $type 可以为show 或 list
     * @param number $mid 模型ID
     * @param string $use_default 当前风格找不到模型参数模板时,是否使用不带模型参数的母模板, 如果非default风格的话,就设置为false,让他好选择default目录相应的带模型参数的模板
     * @return string
     */
    protected function get_auto_tpl($type='show',$mid=0,$use_default=true){
        //模型的模板优先级高于母模板
        if(IN_WAP===true){
            $template = $this->check_file('wap_'.$type.$mid);
        }else{
            $template = $this->check_file('pc_'.$type.$mid);
        }
        if(empty($template)){
            $template = $this->check_file($type.$mid);
        }
        
        $model_cfg = model_config($mid);
        if(empty($template) && $model_cfg['keyword']){  //模型设置了关键字的情况,可以使用指定的模板,但优先级比模型ID的低
            if(IN_WAP===true){
                $template = $this->check_file('wap_'.$type.'-'.$model_cfg['keyword']);
            }else{
                $template = $this->check_file('pc_'.$type.'-'.$model_cfg['keyword']);
            }
            if(empty($template)){
                $template = $this->check_file($type.'-'.$model_cfg['keyword']);
            }
        }
        
        //母模板
        if(empty($template) && $use_default===true){
            if(IN_WAP===true){
                $template = $this->check_file('wap_'.$type);
            }else{
                $template = $this->check_file('pc_'.$type);
            }
        }       
        if(empty($template) && $use_default===true){
            $template = $this->check_file($type);
        }
        return $template;
    }
    
    /**
     * 获取下一页的内容
     * @param unknown $id
     * @return mixed|string
     */
    public function next($id)
    {
        $id = $this->model->getNextById($id,'sort');
        if (empty($id)) {
            $this->error('已经到尽头了');
        }
        return $this->show($id);
    }
    
    /**
     * 自定义字段 仅做附件的路径补全处理 , 其它类型不做转义，附件建议自己处理，不要使用系统自动输出
     * @param array $info
     * @return unknown[]|string[]|array[]
     */
    protected function get_field_fullurl(&$info=[]){
        $_field_array = get_field($info['mid']);
        foreach ($_field_array AS $name=>$rs){
            $type = $rs['type'];
            $value = $info[$name];            
//             if($type == 'images'||$type == 'files'){
//                 $detail = explode(',',$value);
//                 $value = []; 
//                 foreach($detail AS $va){
//                     if($type == 'images'){
//                         $va && $value[]['picurl'] = tempdir($va);
//                     }else{
//                         $va && $value[]['url'] = tempdir($va);
//                     }                    
//                 }
//             }elseif($type == 'image'||$type == 'file'||$type == 'jcrop'){
//                 $value && $value = tempdir($value);
//             }elseif($type == 'images2'){
//                 $value = json_decode($value,true);
//                 foreach($value AS $k=>$vs){
//                     $vs['picurl'] = tempdir($vs['picurl']);
//                     $value[$k] = $vs;
//                 }
//             }
            if(in_array($type,['images','files2','files','image','file','jcrop','images2'])){
                $info['_'.$name] = $value;
                $value = \app\common\field\Show::format_url($rs,$info);                
            }elseif(empty($rs['index_hide'])){
                $info['_'.$name] = $value;
                $value = \app\common\field\Show::get_field($rs,$info)['value'];
            }
            $info[$name] = $value;
            
            $field_array[$name] = [
                    'type'=>$type,
                    'name'=>$name,
                    'title'=>$rs['title'],
                    'value'=>$value,
                    'options'=>$rs['options'],
            ];
        }
        return $field_array;
    }
    
    public function add($fid=0,$mid=0){
        if(!$mid && !$fid){
            $this->error('参数不存在！');
        }elseif($fid){ //根据栏目选择发表内容
            $mid = $this->model->getMidByFid($fid);
            if(empty($mid)){
                $this->error('分类不存在!');
            }
        }
        $this->assign('fid',$fid);
        $this->assign('mid',$mid);
        //$template = $this->get_tpl('post',$this->mid);
        return $this->fetch('post');
    }
    
    public function choose(){
        
    }
    
    public function edit($id=0){
        if (empty($id)) $this -> error('缺少参数');
        $info = $this -> getInfoData($id);
        if (empty($info)) $this -> error('内容不存在');
        $this->assign('info',$info);
        $this->assign('id',$id);
        $this->assign('mid',$info['mid']);
        $this->assign('fid',$info['fid']);
        return $this->fetch('post');
    }
    
}