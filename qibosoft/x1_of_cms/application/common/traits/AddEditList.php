<?php
namespace app\common\traits;

trait AddEditList {
    
    /**
     * 默认列表页
     * @return mixed|string
     */
    public function index() {
        if ($this->request->isPost()) {
            //修改排序
            return $this->edit_order();
        }
        $listdb = $this->getListData($map = [], $order = '');
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 默认发布页
     * @return mixed|string
     */
    public function add() {
        if(ENTRANCE!=='admin'){
            return $this->error('非后台调用,请重写此add方法!');
        }
        return $this -> addContent();
    }
    
    /**
     * 修改页
     * @param unknown $id
     * @return mixed|string
     */
    public function edit($id = null) {
        if(ENTRANCE!=='admin'){
            return $this->error('非后台调用,请重写此edit方法!');
        }
        if (empty($id)) $this -> error('缺少参数');
        $info = $this -> getInfoData($id);
        return $this -> editContent($info);
    }
    
    /**
     * 默认删除功能
     * @param unknown $ids
     */
    public function delete($ids = null) {
        if(ENTRANCE!=='admin'){
            return $this->error('非后台调用,请重写此delete方法!');
        }
        if ($this -> deleteContent($ids)) {
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        }
    }
    
    /**
     * 列表要显示的数据
     * @param array $map 查询条件
     * @param string $order 排序方式
     * @param unknown $rows 每页显示多少条
     * @return unknown
     */
    protected function getListData($map = [], $order = '',$rows=20,$pages=[]) {
        $map = array_merge($this -> getMap(), $map);
        
        $order = $this -> getOrder() ? $this -> getOrder() : $order ;
        if (empty($order)) {
            $data_list = $this -> model -> where($map) -> orderRaw('1 desc') -> paginate(
                    empty($rows)?null:$rows,    //每页显示几条记录
                    empty($pages[0])?false:$pages[0],
                    empty($pages[1])?['query'=>input('get.')]:$pages[1]
                    );
        }else{
            $data_list = $this -> model -> where($map) -> order($order) -> paginate(
                    empty($rows)?null:$rows,    //每页显示几条记录
                    empty($pages[0])?false:$pages[0],
                    empty($pages[1])?['query'=>input('get.')]:$pages[1]
                    );
        }
        return $data_list;
    }
    
    /**
     * 自动生成后台列表页模板,并把数据显示出来
     * @param array $data_list
     * @return mixed|string
     */
    protected function getAdminTable($data_list = []) {
        
        if (empty($this->mid)&&empty($this -> list_items)) {
            $this->error('缺少字段参数list_items');
        }
        
        $template = $this->get_template('',$this->mid);
        if (empty($template)) {
            $template = $this->get_template('admin@common/wn_table');  //如果是前台的话,可以考虑换成 member@common/wn_table 不过最好还是单独设置模板更个性化
        }
        
        $this->tab_ext['right_button'] = $this->builder_rbtn_url($this->tab_ext['right_button']);
        isset($this->tab_ext['top_button']) || $this->tab_ext['top_button'] = [['type'=>'add'],['type'=>'delete']];   //如果没设置顶部菜单 就给两个默认的
        $this->tab_ext['top_button'] = $this->builder_topbtn_url($this->tab_ext['top_button']);
        $pages = is_object($data_list) ? $data_list->render() : '';
        $array = getArray($data_list);
        $this->assign('listdb',isset($array['data'])?$array['data']:$array);
        $this->assign('mid',$this->mid);
        $this->assign('tab_ext',$this->tab_ext);
        $this->assign('f_array',$this->list_items);
        $this->assign('pages',$pages);
        $this->assign('search_file',$this->tab_ext['search_file'] ?: ($this->get_template('search_inc')?:$this->get_template('admin@common/search_inc')));
        return $this->fetch($template);
    }
    
    /**
     * 自动生成会员中心列表页模板,并把数据显示出来
     * @param array $data_list
     * @return mixed|string
     */
    protected function getMemberTable($data_list = []) {
        
        if (empty($this->mid)&&empty($this -> list_items)) {
            $this->error('缺少字段参数list_items');
        }
        
        $template = $this->get_template('',$this->mid);
        if (empty($template)) {
            $template = $this->get_template('admin@common/wn_table');  //如果是前台的话,可以考虑换成 member@common/wn_table 不过最好还是单独设置模板更个性化
        }
        
        $this->tab_ext['right_button'] = $this->builder_rbtn_url($this->tab_ext['right_button']);
        isset($this->tab_ext['top_button']) || $this->tab_ext['top_button'] = [ ['type'=>'delete'], ];   //如果没设置顶部菜单 就给个删除按钮,不给新增加按钮
        $this->tab_ext['top_button'] = $this->builder_topbtn_url($this->tab_ext['top_button']);
        $pages = is_object($data_list) ? $data_list->render() : '';
        $array = getArray($data_list);
        $this->assign('listdb',isset($array['data'])?$array['data']:$array);
        $this->assign('mid',$this->mid);
        $this->assign('tab_ext',$this->tab_ext);
        $this->assign('f_array',$this->list_items);
        $this->assign('pages',$pages);
        $this->assign('search_file',$this->tab_ext['search_file'] ?: TEMPLATE_PATH.'admin_style/default/admin/common/search_inc.htm');
        return $this->fetch($template);
    }
    
    /**
     * 后台查看详情
     * @param array $info
     * @return unknown
     */
    protected function getAdminShow($info = []) {
        
        if (empty($this->mid)&&empty($this -> form_items)) {
            $this->error('缺少字段参数form_items');
        }
        
        $template = $this->get_template('',$this->mid);
        if (empty($template)) {
            $template = $this->get_template('admin@common/show');
        }
        $this->assign('tab_ext',$this->tab_ext);
        $this->assign('f_array',$this->form_items);
        $this->assign('info',$info);
        $this->assign('mid',$this->mid);
        return $this->fetch($template);
    }
    
    /**
     * 辅栏目添加内容
     * @param array $map
     * @param array $order
     * @return mixed|string
     */
    protected function addCategoryInfo($map = [], $order = []) {
        // 显示的字段信息
        $tab_list = $this -> getListItems();
        // 数据内容
        $data_list = $this -> getListData($map, $order);
        
        $template = $this->get_template('',$this->mid);
        if (empty($template)) {
            $template = $this->get_template('admin@common/wn_table');
        }
        
        $this->assign('listdb',$data_list);
        $this->assign('mid',$this->mid);
        return $this->fetch($template);
    }
    
    /**
     * 保存新增数据
     * @return unknown|boolean
     */
    protected function saveAddContent($data=[]) {
        // 保存数据
        if ($this -> request -> isPost()) {
            // 表单数据
            $data || $data = $this -> request -> post();
            
            if (!empty($this -> validate)) {
                // 验证
                $result = $this -> validate($data, $this -> validate);
                if (true !== $result) $this -> error($result);
            }
            $data['uid'] = $this -> user['uid'];
            $data['posttime'] = $data['create_time'] = time();
            if ($result = $this -> model -> create($data)) {
                $this->end_act('add',$data);
                return $result; //$result->id 方便其它地方通过这个得到新的ID
            } else {
                return false;
            }
        }
    }
    
    /**
     * 新发表内容,可以自动生成表单与处理提交的数据
     * @param string $url
     * @param array $vars
     * @return mixed|string
     */
    protected function addContent($url = 'index', $vars = []) {
        // 保存数据
        if ($this -> request -> isPost()) {
            if ($this -> saveAddContent()) {
                $this -> success('添加成功', $url);
            } else {
                $this -> error('添加失败');
            }
        }
        $template = $this->get_template('',$this->mid); //如果模板存在的话,就用实际的后台模板
        if (empty($template)) {
            $template = $this->get_template('admin@common/wn_form');
        }
        // 		if (empty($this->mid)&&empty($this -> form_items)) {
        // 		    $this->error('缺少字段参数form_items');
        // 		}
        $this->assign('mid',$this->mid);
        $this->assign('f_array',$this -> form_items);
        $this->assign('tab_ext',$this->tab_ext);
        return $this->fetch($template,$vars);
    }
    
    
    /**
     * 修改时候的原始数据
     * @param number $id
     * @return array|unknown|NULL[]
     */
    protected function getInfoData($id = 0) {
        return getArray( $this -> model -> get($id));
    }
    
    /**
     * 保存修改时的数据
     * @return boolean
     */
    protected function saveEditContent() {
        // 表单数据
        $data = $this -> request -> post();
        // 验证
        if (!empty($this -> validate)) {
            // 验证
            $result = $this -> validate($data, $this -> validate);
            if (true !== $result) $this -> error($result);
        }
        
        if ($this -> model -> update($data)) {
            $this->end_act('edit',$data);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 修改内容 并且自动生成网页模板
     * @param array $info 要修改的内容数据数组
     * @param string $url 修改成功后跳转的网址
     * @param string $type 前台还是后台模板
     * @return mixed|string
     */
    protected function editContent($info=[], $url = 'index', $type = 'admin') {
        // 保存数据
        if ($this -> request -> isPost()) {
            if ($this -> saveEditContent()) {
                $this -> success('修改成功', $url);
            } else {
                $this -> error('修改失败');
            }
        }
        
        // 		if (empty($this->mid)&&empty($this -> form_items)) {
        // 		    $this->error('缺少字段参数form_items');
        // 		}
        
        if ($this->tab_ext['template'] && is_file($this->tab_ext['template'])) {
            $template = $this->tab_ext['template'];
        }else{
            $template = $this->get_template('',$this->mid);
            if (empty($template)) {
                $template = $this->get_template('admin@common/wn_form');
            }
        }
        
        $this->assign('info',$info);
        $this->assign('f_array',$this -> form_items);
        $this->assign('mid',$this->mid);
        $this->assign('tab_ext',$this->tab_ext);
        return $this->fetch($template);
    }
    
    /**
     * 删除内容 可以用数据传值,同时删除多个
     * @param unknown $ids
     * @return boolean
     */
    protected function deleteContent($ids) {
        if (empty($ids)) {
            $this -> error('ID有误');
        }
        
        $ids = is_array($ids)?$ids:[$ids];
        if (empty($ids)) {
            return false;
        }
        if ($this -> model -> destroy($ids)) {
            $this->end_act('delete',$ids);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 执行完add edit delete 之后要操作的扩展
     * @param string $type 他的值分类是add edit delete 
     * @param array $data
     */
    protected function end_act($type='',$data=[]){        
    }
    
    
    /**
     * 列表页修改排序
     */
    protected function edit_order(){
        $data = $this->request->Post();
        foreach($data['orderdb'] AS $id=>$list){
            $map = [
                    'id'=>$id,
                    'list'=>$list
            ];
            $this->model->update($map);
        }
        $this->success('修改成功');
    }
    
	
	/**
	 * 取模板路径
	 * @param string $type 方法名,也即文件名
	 * @param string $mid 模型ID,可为空
	 * @return string
	 */
	protected static function get_template($type='',$mid=''){
	    if($type==''){
	        if(defined('IN_PLUGIN')){
	           $type = input('param.plugin_action');
	       }else{
	           $type = request()->action();
	       }	        
	    }
	    //当前风格的模板
	    $template = static::search_tpl($type,$mid);
	    
	    if (empty($template)) { //新风格找不到的话,就寻找默认default模板
	        if( config('template.default_view_base') ){ //没有使用默认风格
	            $view_base = config('template.view_base');
	            $index_style = config('template.index_style');
	            $admin_style = config('template.admin_style');
	            config('template.view_base',config('template.default_view_base'));
	            config('template.index_style','default');   // check_file 此方法要用到
	            config('template.admin_style','default');
	            $template = static::search_tpl($type,$mid);
	            config('template.view_base',$view_base);
	            config('template.index_style',$index_style);
	            config('template.admin_style',$admin_style);
	        }
	    }
	    return $template;
	}
	
	/**
	 * 补全菜单参数,并且处理url href 混乱的问题
	 * @param array $array
	 * @return unknown
	 */
	protected function builder_topbtn_url($array=[]){
	    if ($array) {
	        foreach($array AS $key=>$rs){	            
	            $rs['title'] = $rs['title'] ?: static::get_top_bottom($rs['type'],'title');
	            $rs['icon'] = $rs['icon'] ?: static::get_top_bottom($rs['type'],'icon');
	            $rs['class'] = $rs['class'] ? str_replace('btn ', '', $rs['class']) : static::get_top_bottom($rs['type'],'class');
	            $url = $rs['url'] ?: $rs['href'];
	            $rs['url'] = $rs['href'] = $url ?: static::get_top_bottom($rs['type'],'href');
	            $array[$key] = $rs;
	        }
	    }
	    return $array;
	}
	
	protected function builder_rbtn_url($array=[]){
	    if ($array) {
	        foreach($array AS $key=>$rs){	            
	            $rs['title'] = $rs['title'] ?: static::get_right_bottom($rs['type'],'title');
	            $rs['icon'] = $rs['icon'] ?: static::get_right_bottom($rs['type'],'icon');
	            $rs['class'] = $rs['class'] ?: static::get_right_bottom($rs['type'],'class');
	            $url = $rs['url'] ?: $rs['href'];
	            $rs['url'] = $rs['href'] = $url ?: static::get_right_bottom($rs['type'],'href');
	            $array[$key] = $rs;
	        }
	    }
	    return $array;
	}
	
	/**
	 * 查找路径
	 * @param string $type 方法名,也即文件名
	 * @param string $mid 模型ID,可为空
	 * @return string
	 */
	protected static function search_tpl($type='',$mid=''){
	    $filename = $type.$mid;
// 	    if(preg_match('/^([-\w]+)$/i', $type)){    // 比如 $type='index@xxx' 就不适合了 2018-6-21 12:00改过
// 	        static $path_array = [];
// 	        $path = $path_array[config('template.view_base')];
// 	        if(empty($path)){  //避免反复找路径
// 	            $path_array[config('template.view_base')] = $path = dirname( makeTemplate('index',false) ).'/'; //取得路径
// 	        }
// 	        $file = $path . $filename . '.' . ltrim(config('template.view_suffix'), '.');
// 	    }else{
	        $file = makeTemplate($filename);  // 比如 $type='index@xxx'  这种情况
// 	    }	    
        
	    if(is_file($file)){
	    //if(is_file($file)&&filesize($file)){
	        return $file;
	    }elseif($mid!==''){ //寻找母模板
	        //$file = $path . $type . '.' . ltrim(config('template.view_suffix'), '.');
	        $file = makeTemplate($type);
	        if(is_file($file)){
	        //if(is_file($file)&&filesize($file)){
	            return $file;
	        }
	    }
	}
	 
	
	protected static function get_right_bottom($type='',$attr=''){
	    $title = [
	            'delete'=>'删除',
	            'edit'=>'修改',
	    ];
	    $icon = [
	            'delete'=>'fa fa-times',
	            'edit'=>'fa fa-pencil',
	    ];
	    $class = [
	            'delete'=>'btn btn-xs btn-default',
	            'edit'=>'btn btn-xs btn-default',
	    ];
	    $href = [
	            'delete'=>auto_url('delete',['ids' => '__id__']),
	            'edit'=>auto_url('edit',['id' => '__id__']),
	    ];
	    $array = $$attr;
	    return $array[$type];
	}
	
	
	protected static function get_top_bottom($type='',$attr=''){
	    $title = [
	            'add'=>'新增',
	            'delete'=>'删除',
	            'back'=>'返回',
	    ];
	    $icon = [
	            'add'=>'fa fa-plus-circle',
	            'delete'=>'fa fa-times-circle-o',
	            'back'=>'fa fa-reply',
	    ];
	    $class = [
	            'add'=>'',
	            'delete'=>'',
	            'back'=>'',
	    ];
	    $href = [
	            'add'=>auto_url('add'),
	            'delete'=>auto_url('delete'),
	            'back'=>'javascript:history.back(-1);',
	    ];
	    $array = $$attr;
	    return $array[$type];
	}
	
	//下面的方法,尽量别再使用,将要弃用
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 左上角按钮,示例如下
	 
	 $array = [
	 [
	 'title'=>'新增',
	 'url'=>url('add'),
	 'icon'  => 'fa fa-plus-circle',
	 'class' => '',
	 ],
	 [
	 'title'=>'批量删除',
	 'url'=>url('delete'),
	 'icon'  => 'fa fa-microchip',
	 'class' => 'btn btn-danger',
	 ],
	 [
	 'title'=>'其它',
	 'url'=>url('info/index'),
	 'icon'  => 'fa fa-plus-circle',
	 'class' => 'btn btn-danger',
	 ],
	 ];
	 
	 * @param array $array
	 */
	protected function page_topButton($array=[]){
	}
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 信息列表右边按钮,示例如下
	 
	 $array = [
	 [
	 'title'=>'新增',
	 'url'=>url('add'),
	 'icon'  => 'fa fa-plus-circle',
	 'class' => '',
	 ],
	 [
	 'title'=>'批量删除',
	 'url'=>url('delete'),
	 'icon'  => 'fa fa-microchip',
	 'class' => 'btn btn-danger',
	 ],
	 [
	 'title'=>'其它',
	 'url'=>url('info/index'),
	 'icon'  => 'fa fa-plus-circle',
	 'class' => 'btn btn-danger',
	 ],
	 ];
	 
	 * @param array $array
	 */
	protected function page_rightButton($array=[]){
	}
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 页面标题
	 * @param string $title
	 */
	protected function page_title($title='内容管理'){
	}
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 列表要显示的字段信息,举例如下:
	 
	 $array = [
	 ['title', '字段名称', 'text'],
	 ['name', '字段变量名', 'text'],
	 ['type', '表单类型', 'select',config('form')],
	 ['list', '排序值', 'text.edit'],
	 ];
	 
	 * @param array $array
	 */
	protected function page_list_field($array=[]){
	}
	
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 表单中某些字段选中后隐藏或显示另外的字段事件
	 
	 $array = [
	 ['type', '1,2', 'age'],
	 ];
	 * @param array $array
	 */
	protected function page_form_trigger($array=[]){
	}
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 表单页要显示的字段信息,举例如下:
	 
	 $array = [
	 ['text', 'name', '字段变量名','创建后不能随意修改,否则会影响其它地方的数据调用,只能数字或字母及下画线，但必须要字母开头',"title_".rand(0,100)],
	 ['text', 'title', '字段名称'],
	 ['select', 'type', '表单字段类型','',config('form'),'text'],
	 ['textarea', 'options', '参数选项', '用于单选、多选、下拉等类型'],
	 ['text', 'value', '字段默认值'],
	 ['text', 'field_type', '数据库字段类型','','varchar(128) NOT NULL'],
	 ['radio', 'listshow', '是否在列表显示', '', ['不在列表显示', '显示'], 0],
	 ['radio', 'ifsearch', '是否作为内容搜索选项', '', ['否', '是'], 0],
	 ['radio', 'ifmust', '是否属于必填项', '', ['可不填', '必填'], 0],
	 ['text', 'list', '排序值'],
	 ['text', 'nav', '分组名[:对于不重要的字段,你可以添加组名,让他在更多那里显示]'],
	 ];
	 
	 * @param array $array
	 */
	protected function page_form_field($array=[]){
	}
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 表单页填写的字段，参数为true的话，表单里要带上ID，一起提交，好核对要更新哪条主键记录
	 * @param string $isEdit
	 * @return string[]
	 */
	protected function getFormItems($isEdit = false) {
	    // 表单页填写的字段
	    $tab_list = $this -> form_items;
	    
	    if ($isEdit) {
	        // 修改的时候，增加一个隐藏ID，如果主键不是ID的话，要特别指定
	        $tab_list[] = [
	                'hidden',
	                empty($this -> model -> pk) ? 'id' : $this -> model -> pk,
	        ];
	    }
	    return $tab_list;
	}
	
	/**
	 * ##尽量别再使用,将要弃用##
	 * 列表页默认显示字段 
	 * @return array
	 */
	protected function getListItems() {
	    $tab_list = [
	            ['id', 'ID'],
	    ];
	    // 列表页设置 $this->tab_ext['id'] = false;可以隐藏ID这一列，如果主键名不是ID的话，可以重新定义
	    if (isset($this -> tab_ext['id'])) {
	        if (!empty($this -> tab_ext['id'])) {
	            $tab_list = [
	                    [$this -> tab_ext['id'], 'ID'],
	            ];
	        } else {
	            $tab_list = [];
	        }
	    }
	    
	    $tab_list = array_merge($tab_list , $this -> list_items , end($this -> list_items)[0]=='right_button'?[]:[['right_button', '操作', 'btn']]);
	    
	    return $tab_list;
	}
	
	
} 
