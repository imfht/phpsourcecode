<?php
namespace app\common\util;
use app\common\traits\AddEditList;
use app\common\controller\IndexBase;

class Tabel extends IndexBase{
    
    use AddEditList;
    protected static $instance;
    protected $listdb = [];   //列表数据
    
    /**
     * 创建显示列表的table表格
     * @param array $data_list 从数据库取出的列表数据
     * @param array $tab_list 表格参数
     * 比如 [   ['title','标题','text']  , ['mid','模型名称','select2','',[1=>'文章',2=>'图片'] ] ]
     */
    public static function make($data_list=[],$tab_list=[],$right_button='',$top_button_delete=true){
	    if (is_null(self::$instance)) {
	        self::$instance = new static();
	    }
	    self::$instance->list_items = $tab_list;
	    self::$instance->listdb = $data_list;
	    return self::$instance;
	}
	
	/**
     * 添加一列
     * @param string $name 字段变量名称
     * @param string $title 列标题
     * @param string $type 数据类型 比如 text select select2 text.edit
     * @param string $default 默认值
     * @param string $param 额外参数，常用为数组
     * @param string $class css类名
     * @return $this
     */
	public static function addList($name = '', $title = '', $type = '', $default = '', $param = '', $class = ''){
	    self::$instance->list_items[] = [$name, $title, $type, $default, $param, $class];
	    return self::$instance;
	}
	
	/**
	 * 一次性添加多列
	 * @param array $array 
	 * 比如 [['title','标题','text'],['mid','模型名称','select2','',[1=>'文章',2=>'图片']]],]
	 * 第三项常用类型 比如 text select select2 text.edit
	 * 第四项是默认值，第五项是参数，常用为数组
	 * @return $this
	 */
	public static function addLists($array=[]){
	    self::$instance->list_items = is_array(self::$Btable->list_items) ? array_merge(self::$Btable->list_items , $array) : $array ;
	    return self::$instance;
	}
	
	/**
	 * 设置分组导航列表
	 * @param array $tab_list Tab列表  [1=>['title' => '标题A', 'url' => 'http://xxxx.com'],2=>['title' => '标题B', 'url' => 'http://xxxx.com']]
	 * @param string $curr_id 当前分组ID
	 * @return $this
	 */
	public static function addNav($tab_list = [], $curr_id = ''){
	    self::$instance->tab_ext['nav'] = [$tab_list,$curr_id];
	    return self::$instance;
	}
	
	/**
	 * 设置页面提示
	 * @param string $msg 提示信息
	 * @param string $type 提示类型：success/info/warning/danger，默认info
	 * @return $this
	 */
	public static function addPageTips($msg = '', $type = 'info'){
	    self::$instance->tab_ext['help_msg'] = $msg;
	    return self::$instance;
	}
	
	/**
	 * 设置页面标题
	 * @param string $title 页面标题
	 * @return $this
	 */
	public static function addPageTitle($title = ''){
	    self::$instance->tab_ext['page_title'] = $title;
	    return self::$instance;
	}
	
	/**
	 * 添加一个顶部按钮
	 * @param string $type 按钮类型：add/enable/disable/back/delete/custom
	 * @param array $attribute 按钮样式属性
	 * @param bool $blank 是否使用弹出新窗口
	 * @return $this
	 */
	public static function addTopButton($type = '', $attribute = [], $blank = false){
	    $attribute['href'] || $attribute['href'] = $attribute['url'];
	    self::$instance->tab_ext['top_button'][] = [
	            'title' => $attribute['title'] ?: self::get_top_bottom($type,'title'),
	            'icon'  => $attribute['icon'] ?: self::get_top_bottom($type,'icon'),
	            'class' => $attribute['class'] ?: self::get_top_bottom($type,'class'),
	            'href'  => $attribute['href'] ?: self::get_top_bottom($type,'href'),
	            'type'  => $type,
	    ];
	    return self::$instance;
	}
	
	/**
	 * 添加一个右侧按钮
	 * @param string $type 按钮类型：edit/enable/disable/delete/custom
	 * @param array $attribute 按钮属性
	 * 例如 ['title' => '添加','icon' => 'fa fa-plus', 'data-tips' => '删除后无法恢复。','class' => 'btn', 'href' => url('add', ['id' => '__id__']),]
	 * 
	 * @param bool $blank 是否使用弹出新窗口
	 * @return $this
	 */
	public static function addRightButton($type = '', $attribute = [], $blank = false){
	    if($type=='add'){
	        $attribute = array_merge(
	                ['title' => '添加','icon' => 'fa fa-plus', 'data-tips' => '删除后无法恢复。','class' => 'btn', 'href' => url('add', ['id' => '__id__']),],
	                $attribute
	                );
	    }
	    $attribute['href'] || $attribute['href'] = $attribute['url'];
	    self::$instance->tab_ext['right_button'][] = [
	            'title' => $attribute['title'] ?: self::get_right_bottom($type,'title'),
	            'icon'  => $attribute['icon'] ?: self::get_right_bottom($type,'icon'),
	            'class' =>$attribute['class'] ?: self::get_right_bottom($type,'class'),
	            'href'  => $attribute['href'] ?: self::get_right_bottom($type,'href'),
	            'type'  =>$type,
	    ];
	    return self::$instance;
	}
	
	/**
	 * 一次性添加多个右侧按钮
	 * @param array|string $buttons 按钮类型
	 * 例如： 'edit' 或 'edit,delete' 或 ['edit', 'delete'] 或 ['delete','edit' => ['title' => '修改']]
	 * @return $this
	 */
	public static function addRightButtons($button_array = []){
	    self::$instance->tab_ext['right_button'] = is_array(self::$Btable->tab_ext['right_button']) ? array_merge(self::$Btable->tab_ext['right_button'],$button_array) : $button_array;
	    return self::$instance;
	}
	
	/**
	 * 添加表头排序
	 * @param array|string $column 表头排序字段，多个以逗号隔开
	 * @return $this
	 */
	public static function addOrder($column=[]){
	    return self::$instance;
	}
	
	/**
     * 设置搜索参数
     * @param array $fields 参与搜索的字段
     * @param string $placeholder 提示符
     * @param string $url 提交地址
     * @param null $search_button 提交按钮
     * @return $this
     */
	public static function addSearch($fields = [], $placeholder = '', $url = '', $search_button = null){
	    self::$instance -> tab_ext['search'][] = [$fields=>$placeholder];
	    return self::$instance;
	}
	
	/**
     * 添加表头筛选
     * @param array|string $columns 表头筛选字段，多个以逗号隔开
     * @param array $options 选项，供有些字段值需要另外显示的，比如字段值是数字，但显示的时候是其他文字。
     * @param array $default 默认选项，['字段名' => '字段值,字段值...']
     * @param string $type 筛选类型，默认为CheckBox，也可以是radio
     * @return $this
     */
	public static function addFilter($columns = [], $options = [], $default = [], $type = 'radio'){
	    self::$instance -> tab_ext['filter_search'][] = $columns;
	    return self::$instance;
	}
	
	/**
     * 添加表头筛选列表
     * @param string $field 表头筛选字段
     * @param array $list 需要显示的列表
     * @param string $default 默认值，一维数组或逗号隔开的字符串
     * @param string $type 筛选类型，默认为CheckBox，也可以是radio
     * @return $this
     */
	public static function addFilterList($field = '', $list = [], $default = '', $type = 'radio'){
	    self::$instance->addFilterList($field, $list, $default, $type);
	    return self::$instance;
	}
	
	/**
	 * 加载模板输出
	 * @param string $template 模板文件名
	 * @param array  $vars     模板输出变量
	 * @param array  $replace  模板替换
	 * @param array  $config   模板参数
	 * @return mixed
	 */
	public static function fetchs(){
	    return self::$instance->getAdminTable(self::$instance->listdb);
	}
	
}