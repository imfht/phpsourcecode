<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\Plugin as PluginModel;
use app\common\traits\Market;
use app\common\model\Hook_plugin AS HP_Model;

class Plugin extends AdminBase
{
    use AddEditList,Market;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'page_title'=>'插件管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new PluginModel();
		$this->tab_ext = [
		    'page_title'=>'插件管理',
		    'top_button'=>[
		        [
		            'title'=>'安装本地新插件',
		            'url'=>url('add'),
		            'icon'  => 'fa fa-plus-circle',
		            'class' => '',
		        ],
		        [
		            'title'=>'安装应用市场插件',
		            'url'=>url('market'),
		            'icon'  => 'fa fa-cloud-download',
		            'class' => '',
		        ],
		    ],
		];
		$this->list_items = [
		        ['icon', '图标', 'icon'],
				['name', '插件名称', 'text'],                
				['keywords', '插件关键字', 'text'],
				['ifopen', '启用或停用', 'yesno'],
				['author', '开发者', 'link','__author_url__','blank'],
				['list', '排序值', 'text.edit'],
		        ['version', '版本更新', 'callback',function($value=''){
		            list($time) = explode("\t",$value);
		            return $time;
		        }],
    		    ['right_button', '操作', 'callback',function($value,$rs){
    		        return ($rs['type']==0?'':'<a title="复制当前插件" icon="fa fa-copy" class="btn btn-xs btn-default" href="'.url('copy',['id'=>$rs['id']]).'" target="_self"><i class="fa fa-copy"></i></a>').$value;
    		    },'__data__'],
			];
	}
	
	public function index() {
	    if ($this->request->isPost()) {
	        //修改排序
	        return $this->edit_order();
	    }
	    $listdb = $this->getListData($map = [], $order = [],$rows=50);
	    return $this -> getAdminTable($listdb);
	}
	
	/**
	 * 卸载插件
	 * @param number $ids
	 */
	public function delete($ids=0){
	    $result = $this->uninstall($ids,'p');
	    if ($result!==true){
	        $this->error($result);
	    }else{
	        $this->success('卸载成功');
	    }	   
	}
	
	public function add($keywords='')
    {        
        /*$this->form_items = [
					['text', 'keywords', '关键字'],
					['text', 'name', '插件名称'],					
				];
        return $this->addContent();*/
        if ($keywords!='') {
            $result = $this->install($keywords,'p');
            if ($result!==true) {
                $this->error($result);
            }else{
                $this->success('插件安装成功,请设置一下后台权限',url('group/admin_power',['id'=>$this->user['groupid']]));
            }            
        }
        
        $this->tab_ext['right_button'] = [['type'=>'delete']];
        $this->list_items = [
            ['icon', '图标', 'icon'],
            ['name', '系统名称', 'text'],
            //['pre', '数据表前缀', 'text'],
            ['keywords', '关键字(目录名)', 'text'],
            ['right_button', '安装插件', 'callback',function($value,$rs){
                return '<a title="安装当前插件" icon="fa fa-plus" class="btn btn-xs btn-default" href="'.url('add',['keywords'=>$rs['keywords']]).'" target="_self"><i class="fa fa-plus"></i></a>';
            },'__data__'],
            ];
        $array = plugins_config();
        foreach($array AS $key=>$rs){
            $exists_modules[$rs['keywords']] = $rs;
        }
        $dir = opendir(PLUGINS_PATH);
        while ($file = readdir($dir)){
            if($file!='.' && $file!='..' && is_dir(PLUGINS_PATH.$file) && is_file(PLUGINS_PATH."$file/install/info.php") && empty($exists_modules[$file])){
                $modules[] = include PLUGINS_PATH."$file/install/info.php";
            }
        }
        return $this -> getAdminTable($modules);
    }
    
    
    /**
     * 修改模块设置
     * @param number $id
     * @param string $type
     * @return mixed|string
     */
    public function edit($id = 0,$type='')
	{
	    if (empty($id)) $this->error('缺少参数');
	    
	    if($type=='refresh'){
	        $data = [
	                'id'=>$id,
	                'version'=>'',
	        ];
	        $this->model->update($data);
	        \think\Cache::clear();
	        $this -> success('刷新成功',url('admin/upgrade/index'));
	    }
	    
	    $info = $this->getInfoData($id);
	    
	    if ($this -> request -> isPost()) {
	        $this -> request -> post(['admingroup'=>implode(',', $this -> request -> post()['admingroup']?:[])]);
	        if ($this -> saveEditContent()) {
	            
	            //钩子要对应的跟着关闭或启用
	            $data = $this -> request -> post();
	            HP_Model::where('plugin_key',$info['keywords'])->update(['ifopen'=>$data['ifopen']]);
	            HP_Model::where('hook_class','like',"plugins\\\\".$info['keywords']."\\\%")->update(['ifopen'=>$data['ifopen']]);
	            cache('hook_plugins', NULL);
	            
	            $this -> success('修改成功', 'index');
	        } else {
	            $this -> error('修改失败');
	        }
	    }
	    
	    $this->form_items = [	           
	        ['text','name', '插件名称', ''],
	        ['static','keywords', '插件关键字', ''],
	        ['radio','ifopen', '启用或禁用', '',['关闭系统','启用系统']],	            
	        ['number','list', '排序值', ''],
	        ['icon','icon', '图标', ''],
	        ['radio','is_sell', '是否上架应用市场','',['不上架','上架']],
	        ['number','testday', '可以试用几天'],
	        ['textarea','money', '售价', '格式如下,第一项是天数,第二项是价格(元),第三项是名称:<br>30|5|一个月<br>180|10|半年'],
	        ['checkbox','admingroup','免费使用的用户组','管理员的话,长久有效,VIP会员的话,使用期限跟VIP的时效一致.',getGroupByid()],
	        ['image','picurl', '封面图'],
	        ['textarea','about', '介绍'],
	    ];
	    
	    $this->tab_ext['trigger'] = [
	        ['is_sell', '1', 'testday,money,admingroup,picurl,about'],
	    ];
	    
	    return $this->editContent($info);
	}
	
	
	/**
	 * 执行插件内部方法
	 * @return mixed
	 */
	public function execute()
	{
	    $plugin     = input('param.plugin_name');
	    $controller = input('param.plugin_controller');
	    $action     = input('param.plugin_action');
	    define('IN_PLUGIN', $plugin .'#' . $controller .'#' . $action);
        
	    //参数优先级为 POST>GET>ROUTE
	    $get_params = input('get.');
	    $post_params = input('post.');
	    $params     = $this->request->except(['plugin_name', 'plugin_controller', 'plugin_action'], 'route');
	    if($get_params){
	        $params = $params?array_merge($params,$get_params):$get_params;
	    }
	    if($post_params){
	        $params = $params?array_merge($params,$post_params):$post_params;
	    }
	    
	    if (empty($plugin) || empty($controller) || empty($action)) {
	        $this->error('没有指定插件名称、控制器名称或操作名称');
	    }
	    
	    if (!plugin_action_exists($plugin, $controller, $action)) {
	            $this->error("找不到类及方法：plugins\\{$plugin}\\admin\\".format_class_name($controller));
	    }
	    return plugin_action($plugin, $controller, $action, $params);
	}
	
	
	/**
	 * 应用市场
	 */
	public function market($id=0,$page=0){
	    //执行安装云端模块
	    if($id){
	        return $this->getapp($id,'p');
	    }
	    $this->assign('fid',2);	
	    return $this->fetch();
	}
	
	/**
	 * 复制插件
	 * @param number $id
	 * @return mixed|string
	 */
	public function copy($id=0){
	    if (empty($id)) $this->error('缺少参数');
	    
	    $info = $this->getInfoData($id);
	    
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        $str = 'abstract,and,array,as,break,callable,case,catch,class,clone,php,const,continue,declare,default,die,do,echo,else,elseif,empty,enddeclare,endfor,endforeach,endif,endswitch,endwhile,eval,exit,extends,final,finally,for,foreach,function,global,goto,if,implements,include,instanceof,interface,isset,list,namespace,new,print,private,protected,public,require,return,static,switch,throw,trait,try,unset,use,var,while,xor,yield,insteadof';
	        if(in_array($data['keywords'],explode(',',$str))){
	            $this->error('请换一个目录名,该目录名是PHP关键字');
	        }
	        $reuslt = $this->copy_mod($info,$data,'p');
	        if($reuslt!==true){
	            $this->error($reuslt);
	        }else{
	            $this->success('模块复制成功,请设置一下后台权限',url('group/admin_power',['id'=>$this->user['groupid']]));
	        }
	    }
	    
	    $this->form_items = [
	            ['text','name', '新插件名称','', $info['name'].'-XXX'],
	            ['text','keywords', '新插件存放目录', '只能字母或数字',$info['keywords'].'xxx'],
	    ];
	    return $this->addContent();
	}

}
