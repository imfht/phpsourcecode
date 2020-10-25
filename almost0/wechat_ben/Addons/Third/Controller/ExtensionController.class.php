<?php

namespace Addons\Third\Controller;
use Addons\Third\Controller\BaseController;

class ExtensionController extends BaseController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'third' );
	
	
		$res ['title'] = '内置';
		$res ['url'] = addons_url ( 'Third://Third/lists' );
		$res ['class'] = '';
		$nav [] = $res;
	
		$res ['title'] = '自定义';
		$res ['url'] = addons_url ( 'Third://Custom/lists'  );
		$res ['class'] = '';
		$nav [] = $res;
		$res ['title'] = '第三方平台';
		$res ['url'] = addons_url ( 'Third://Extension/lists'  );
		$res ['class'] = 'current';
		$nav [] = $res;
	
		$this->assign ( 'nav', $nav );
	}

	public function lists() {
	
		
		$model = $this->getModel ( $this->table );
		$map ['type'] = 2;//类型为第三方
		session ( 'common_condition', $map );
	//	$this->model['list_grid']='keyword:关键词;title:标题;remark:备注;status|get_name_by_status:状态;id:操作:changestatus?id=[id]|修改状态';
		parent::common_lists ( $this->model );
	}
	public function changestatus()
	{
		//$model = $this->getModel ( $this->model );
		$map['id']=I('id');
	    $model = M('third' );
		$status = $model->where ( $map )->getField('status');
	
	//	$model->where($map)->find();
		if($status==0)
		{		
           $model->where ( $map )-> setField('status',1);
		}
		else
		{
			$model->where ( $map )-> setField('status',0);
		}
		
			$this->success ( '修改成功！' );
				
	
	}
	
	
	public function add() {
	
		is_array ( $model ) || $model = $this->getModel ( $model );
		if (IS_POST) {
		//	$_POST ['token'] = get_token ();
			$_POST ['parameters'] =$_POST ['parameters'] ;//转换tonken参数
			
			$_POST ['output_format'] = 1;//原样输出
			$_POST ['requesttype'] = 1;//请求方式为post
			$_POST ['type'] = 2;//类型为第三方
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $id = $Model->add ()) {
				$this->_saveKeyword ( $model, $id );
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
	
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
	
			$templateFile || $templateFile = $model ['template_add'] ? $model ['template_add'] : '';
			$this->display ( $templateFile );
		}
	}
	
	public  function edit()
	{
		is_array ( $model ) || $model = $this->getModel ( $model );
		$id || $id = I ( 'id' );
		
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->save ()) {
				$this->_saveKeyword ( $model, $id );
		
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model ['id'] );
				
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );
				
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
				
			$templateFile || $templateFile = $model ['template_edit'] ? $model ['template_edit'] : '';
			$this->display ( $templateFile );
		}
	}
	

}





