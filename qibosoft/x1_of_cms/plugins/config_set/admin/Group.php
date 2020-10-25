<?php
namespace plugins\config_set\admin;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use plugins\config_set\model\Group as GroupModel;
use app\common\model\Module;
use app\common\model\Plugin;

class Group extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
				'page_title'=>'系统设置分组管理',
				];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new GroupModel();
		$this->list_items = [				              
				['title', '分组名称', 'text'],
		        ['sys_id', '归属', 'callback',function($value){
		            if($value>0){
		                return  '【模块】'.Module::getNameById($value);
		            }elseif($value<0){
		                return  '【插件】'.Plugin::getNameById(abs($value));
		            }else{
		                return '系统';
		            }
		        }],
		        ['ifshow', '统一管理', 'yesno'],
		        ['ifsys', '全局变量', 'yesno'],
		        ['list', '排序值', 'text.edit'],
			];
		$this->form_items =[
		        ['text','title','分类(分组)名称'],
		        ['radio','ifshow','是否在系统核心设置那里统一管理','如果不太重要的就可以单独设置',['不显示','显示'],0],
		        ['radio','ifsys','该分类下的所有参数是否为系统全局变量','频道不能属于,属于系统全局变量的话，变量命名不可与系统里的重复',['不属于','属于'],0],
		        ['select','sys_id','所属系统ID','0代表系统，模块为对应的模块ID值，插件的话为对应的ID负值',$this->module_hack(),0],
		        ['text','list','排序值'],
		];
	}
	
	/**
	 * 列出所有频道与插件,频道为正数,插件为负数
	 * @return string[]
	 */
	private function module_hack(){
	    $array = ['系统全局'];
	    $module = modules_config();
	    foreach($module AS $rs){
	        $array[$rs['id']] = '(模块) '.$rs['name'];
	    }
	    $hack = plugins_config();
	    foreach($hack AS $rs){
	        $array["-{$rs['id']}"] = '(插件) '.$rs['name'];
	    }
	    return $array;
	}
	
	public function index()
	{
	    $this->tab_ext['right_button'] = [
	            ['type'=>'delete'],
	            [
	                    'title'=>'管理',
	                    'icon'=>'fa fa-list',
	                    'url'=>purl('config_set/config/index',['group'=>'__id__']),
	            ],
	            ['type'=>'edit'],	            
	    ];
	    $data = $this->model->order('list','desc')->select();
	    foreach($data AS $key=>$rs){
	        if($rs['sys_id']==0){
	            $rs['ifsys']=1;
	            $data[$key] = $rs;
	        }
	    }
	    return $this->getAdminTable($data);
	}
	
	public function delete($ids = null)
	{
	    is_array($ids) || $ids = [$ids];
	    
	    in_array(1, $ids) && $this->error('第一条记录不能删除');

	    if( $this->deleteContent($ids) ){
	        $this->success('删除成功');
	    }else{
	        $this->error('删除失败');
	    }
	}
	
}
