<?php
namespace plugins\weixin\admin;

use app\common\controller\AdminBase; 

use app\common\traits\AddEditList;

use plugins\weixin\model\WeixinMsg as WeixinMsgModel;

class WeixinMsg extends AdminBase
{
	
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext = [
		    'page_title'=>'公众号用户留言信息',
	        'top_button'=>[ ['type'=>'delete']], //只显示删除按钮
	        'right_button'=>[ ['type'=>'delete']],
	];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new WeixinMsgModel();
		$this->list_items = [				
				['uid', '用户名', 'callback', function($value){
					$rs=get_user($value);
                    return $rs['username'];
                }],                
				['posttime', '留言时间', 'datetime'],
				['content', '留言内容', 'callback', function($value,$data){
                    return $this->model->format_list_data($data['type'],$value,$data['url']);
                },'__data__'], 
				
			];
	}


}
