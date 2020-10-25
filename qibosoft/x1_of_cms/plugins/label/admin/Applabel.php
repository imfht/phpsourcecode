<?php
namespace plugins\label\admin;

use app\common\controller\AdminBase;
use plugins\label\model\Label AS Model;
use app\common\traits\AddEditList;

class Applabel extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;
	protected $group = 'base';
	
	protected function _initialize()
    {
		parent::_initialize();
		
		$this->model = new Model();
	}
	
	/**
	 * 标签列表
	 * @param number $group
	 * @param unknown $sys_id
	 * @return mixed|string
	 */
	public function index($group=0,$sys_id=null)
	{
	    $this->tab_ext = [
	            'page_title'=>'app标签管理',
	    ];
	    
	    $array = $this->get_basemodel();	    
	    $module = $this->get_module();
	    $module && $array = array_merge($array,$module);	    
	    $plugins = $this->get_plugins();
	    $plugins && $array = array_merge($array,$plugins);
	    
	    $this->list_items = [
	            ['name', '关键字', 'text'],
	            ['title', '标签名称', 'text.edit'],
	            ['type', '调用数据类型', 'select',$array],
	            ['list', '排序值', 'text.edit'],
	            ['set', '标签设置','callback' ,function($key,$v){
	                return '<a title="设置标签" icon="fa fa-gear" class="btn btn-xs btn-default pop" href="'.auto_url('set',['type'=>$v['type'],'name'=>$v['name']]).'"><i class="fa fa-gear"></i></a>';
	            },'__data__'],
	            ['view', '标签预览','callback' ,function($key,$v){
	                return '<a title="标签预览" icon="fa fa-gear" class="btn btn-xs btn-default pop" href="'.auto_url('set',['type'=>$v['type'],'name'=>$v['name'],'act'=>'view']).'"><i class="fa fa-fw fa-telegram"></i></a>';
	            },'__data__'],
	    ];
	    
	    $this->tab_ext['top_button'] =[
	            [
	                    'title' => '新增一个标签',
	                    'icon'  => 'fa fa-fw fa-th-list',
	                    'class' => 'btn btn-primary',
	                    'href'  => auto_url('add')
	            ],
	    ];
	    
	    $map = ['if_js'=>1];
	    $data = $this->model->where($map)->order('list','desc')->paginate(50);
	    return $this->getAdminTable( $data );
	}
	
	/**
	 * 设置标签数据调用
	 * @param string $name
	 * @param string $type
	 * @return mixed
	 */
	public function set($name='',$type='',$act=''){
	    
	    cache('config_app_tags',null); //清空配置缓存
	    
	    $base_label = $this->get_basemodel();
	    $module_label = $this->get_module();
	    $plugins_labe = $this->get_plugins();
	    
	    
	    if($base_label[$type]){
	        $url = iurl('index/label/'.$type , ['name'=>$name],true,false,'m');
	    }elseif ($module_label[$type]){
	        $url = iurl($type.'/label/tag_set' , ['name'=>$name],true,false,'m');
	    }elseif ($plugins_labe[$type]){
	        $url = iurl($type.'/label/tag_set' , ['name'=>$name]);
	    }else{
	        $this->error('标签类型数据有误!');
	    }
	    
	    if($act=='view'){
	        $info = getArray($this->model->where('name',$name)->find());
	        $cfg = unserialize($info['cfg']);
	        if ($this->request->isPost()) {
	            $data = $this->request->post();
	            $cfg['showfield'] = $data['postdb'];
	            $result = $this->model->where('name',$name)->update(['cfg'=>serialize($cfg)]);
	            if ($result) {
	                cache('config_app_tags',null);
	                $this->success('修改成功');
	            }	            
	        }
	        $url = $this->request->domain().iurl('index/label_show/app_get' , ['name'=>$name],true,false,'m');	        
	        $this->assign('label_url',$url);
	        $json_code  = file_get_contents($url);
	        $this->assign('json_code',$json_code);
	        $array = json_decode($json_code,true);
	        $array_code = var_export(  $array  ,true);
	        $this->assign('array_code',  str_replace(["\n"],["\n  "], $array_code));
	        $karray = [];
	        if ($array['data'][0]) {
	            foreach($array['data'][0] AS $key=>$v){
	                $karray[] = $key;
	            }
	        }
	        $this->assign('karray',$karray);
	        $this->assign('showfield',$cfg['showfield']);
	        return $this->pfetch('view');
	    }else{
	        echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=$url'>";
	        exit;
	    }	    
	}
	
	/**
	 * 新增加标签
	 * @return mixed|string
	 */
	public function add()
	{
	    $this->set_items();
	    $this->tab_ext = [
	            'page_title'=>'新增标签',
	    ];
	    return $this->addContent();
	}
	
	/**
	 * 填写表单参数选项
	 * @param array $info
	 */
	protected function set_items(){	    

	    $array = $this->get_basemodel();
	    
	    $module = $this->get_module();
	    $module && $array = array_merge($array,$module);
	    
	    $plugins = $this->get_plugins();
	    $plugins && $array = array_merge($array,$plugins);
	    
	    
	    $this->form_items = [
	            ['hidden', 'id'],
	            ['hidden', 'if_js',1],
	            ['text', 'title', '标签名称','可以是汉字'],
	            ['text', 'name', '关键字','创建后不要随意修改,必须是英文或数字,跟之前的不能雷同'],	            
	            ['radio', 'type', '调取数据','',$array],	           
	    ];
	    
	}
	
	private function get_basemodel(){
	    return [
	            'image'=>'image',
	            'images'=>'images',
	            'textarea'=>'纯文本代码',
	            'ueditor'=>'编辑器',
	            'sql'=>'SQL原生查询万能标签',
	            'member'=>'会员模块',
	    ];
	}
	
	
	private function get_module(){
	    $array = [];
	    $dir = opendir(APP_PATH);
	    while (($file = readdir($dir))!==false) {
	        if($file!='.'&&$file!='..'&&is_dir(APP_PATH.$file)){
	            if(is_file(APP_PATH."$file/index/Label.php")){
	                $class = "\\app\\$file\\index\\Label";
	                if(class_exists($class)&&method_exists($class,'tag_set')){
	                    $_ar = modules_config($file);
	                    if($_ar){
	                        $array[$_ar['keywords']] = $_ar['name'];
	                    }
	                }
	            }
	        }
	    }
	    return $array;
	}
	
	private function get_plugins(){
	    $array = [];
	    $dir = opendir(PLUGINS_PATH);
	    while (($file = readdir($dir))!==false) {
	        if($file!='.'&&$file!='..'&&is_dir(PLUGINS_PATH.$file)){
	            if(is_file(PLUGINS_PATH."$file/index/Label.php")){
	                $class = "\\plugins\\$file\\index\\Label";
	                if(class_exists($class)&&method_exists($class,'tag_set')){
	                    $_ar = plugins_config($file);
	                    if($_ar){
	                        $array[$_ar['keywords']] = $_ar['name'];
	                    }
	                }
	            }
	        }
	    }
	    return $array;
	}
	
	
	public function edit($id = null)
	{
	    if (empty($id)) $this->error('缺少参数');	    
	    $this->set_items();	    
	    $info = $this -> getInfoData($id);	    
	    return $this->editContent($info,auto_url('index'));
	}
	


}
