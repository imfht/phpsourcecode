<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\Module_buyer AS BuyerModel;

/**
 * 应用市场购买应用的商家
 *
 */
class Buylist extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [     
			['datetime', 'endtime', '失效日期','留空则长期有效'],			
	];
	protected $list_items;
	protected $tab_ext;	
	protected function _initialize()
    {
        if(!table_field('hook_plugin','hook_key')){
            into_sql(APP_PATH."common/upgrade/2.sql");
        }
		parent::_initialize();
		$this->model = new BuyerModel();
		$this->tab_ext = [
		        'page_title'=>'购买记录',
		        'top_button'=>[
		            ['type'=>'delete']
		        ],
		];
		
	}
	
	public function edit($id = null) {
	    if (empty($id)) $this -> error('缺少参数');
	    $info = $this -> getInfoData($id);
	    if ($this -> request -> isPost()) {
	        $data = $this -> request -> post();
	        
	        $this -> request -> post([
	            'endtime'=>$data['endtime'] ? strtotime($data['endtime']) : 0,
	        ]);
	    }
	    return $this -> editContent($info,url('index',['id'=>$info['mid']]));
	}
	
	public function index($id=0) {
	    $this->list_items = [
	        ['uid', '购买者', 'username'],
	        ['endtime', '失效日期', 'datetime'],
	        ['mid', '应用名称', 'callback',function($key,$rs){
	            $info = $key>0?modules_config($key):plugins_config(abs($key));
	            return $info['name'];
	        },'__data__'],
	        
	        ['create_time', '初次购买日期', 'datetime'],	        
	        
	    ];
	    $map = [];
	    if ($id) {
	        $map = ['mid'=>$id];
        }
        $this -> tab_ext['search'] = [
            'uid'=>'用户UID',
         ];    //支持搜索的字段
	    $listdb = $this->getListData($map, $order = '');
	    return $this -> getAdminTable($listdb);
	}
}
