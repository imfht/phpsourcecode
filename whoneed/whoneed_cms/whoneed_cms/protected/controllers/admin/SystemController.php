<?php
/**
 * 后台系统管理首页
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 *
 */

	class SystemController extends MyAdminController{
		
		private $arrUAuth = array();

		// 初始化
		public function init(){
			parent::init();
		}
		
		// 后台首页
		public function actionIndex(){
			echo 'dss';
		}

		// ========================================== 栏目管理
		// 栏目列表
		public function actionColumn(){
			
			// 获取栏目列表
			$arrDataList = CF::funArrGetColumn();

			$data = array();
			$data['arrDataList'] = $arrDataList;
			$this->display('column', $data);			
		}

		// 添加栏目
		public function actionColumn_add(){
			$fid	= intval(Yii::app()->request->getParam('fid'));

			$data = array();
			$data['fid'] = $fid;
			$this->display('column_add', $data);			
		}

		// 表添加，实际入库
		public function actionColumn_add_save(){			
			// submit
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$column_name	= trim(Yii::app()->request->getParam('column_name'));
				$column_url		= trim(Yii::app()->request->getParam('column_url'));
				$c_order		= floatval(Yii::app()->request->getParam('c_order'));
				$fid			= intval(Yii::app()->request->getParam('fid'));
				$model_id		= intval(Yii::app()->request->getParam('model_id'));
				
				$objDB	= new Whoneed_rbac_column();
				$objDB->fid			= $fid;
				$objDB->column_name	= $column_name;
				$objDB->column_url	= $column_url;
				$objDB->c_order		= $c_order;
				$objDB->model_id	= $model_id;
				
				if($objDB->save()){
					$this->alert_ok();
				}else{
					$this->alert_error();
				}
			}else{
				$this->alert_error();
			}
		}

		// 编辑栏目
		public function actionColumn_edit(){
			$id = intval(Yii::app()->request->getParam('id'));
			if(!$id){
				$this->alert_error('id无效!');
			}
			
			$objData = Whoneed_rbac_column::model()->find("id = $id");
			if(!$objData){
				$this->alert_error('此记录不存在!');
			}

			$data = array();
			$data['objData'] = $objData;
			$this->display('column_edit', $data);			
		}

		// 编辑栏目入库
		public function actionColumn_edit_save(){			
			// submit
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$column_name	= trim(Yii::app()->request->getParam('column_name'));
				$column_url		= trim(Yii::app()->request->getParam('column_url'));
				$c_order		= floatval(Yii::app()->request->getParam('c_order'));
				$fid			= intval(Yii::app()->request->getParam('fid'));
				$id				= intval(Yii::app()->request->getParam('id'));
				$model_id		= intval(Yii::app()->request->getParam('model_id'));

				if(!$id){
					$this->alert_error('id无效!');
				}				

				$objDB = Whoneed_rbac_column::model()->find("id = $id");
				if(!$objDB){
					$this->alert_error('此记录不存在!');
				}
				$objDB->fid			= $fid;
				$objDB->column_name	= $column_name;
				$objDB->column_url	= $column_url;
				$objDB->c_order		= $c_order;
				$objDB->model_id	= $model_id;
				
				if($objDB->save()){
					$this->alert_ok();
				}else{
					$this->alert_error();
				}
			}else{
				$this->alert_error();
			}
		}

		// 删除栏目
		public function actionColumn_delete(){
			$id				= intval(Yii::app()->request->getParam('id'));
			if(!$id){
				$this->alert_error('id无效!');
			}				

			$objDB = Whoneed_rbac_column::model()->find("id = $id");
			if(!$objDB){
				$this->alert_error('此记录不存在!');
			}
			
			if($objDB->delete())
				$this->alert_ok();
			else
				$this->alert_error();
		}

		// ======================== 角色对应栏目
		public function actionRole_column(){
			// 获取栏目列表
			$arrDataList = CF::funArrGetColumn();
			
			// 获取此角色已经拥有的权限
			$role_id = intval(Yii::app()->request->getParam('role_id'));
			$arrAuth = array();
			$objAuth = Whoneed_rbac_role_column::model()->findAll("role_id = {$role_id}");
			if($objAuth){
				foreach($objAuth as $auth){
					$arrAuth[] = $auth->column_id;
				}
			}

			$data = array();
			$data['arrDataList'] = $arrDataList;
			$data['arrAuth']	 = $arrAuth;
			$this->display('role_column', $data);			
		}

		public function actionRole_column_save(){
			// submit
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$column_id	= Yii::app()->request->getParam('column_id');
				$role_id	= intval(Yii::app()->request->getParam('role_id'));

				if($role_id){
					// delete from role_id
					$objDB = new Whoneed_rbac_role_column(); 
					$objDB->deleteAll("role_id = {$role_id}");

					// save
					if($column_id && is_array($column_id)){
						foreach($column_id as $k=>$cid){
							$objDB = new Whoneed_rbac_role_column();
							$objDB->role_id		= $role_id;
							$objDB->column_id	= $cid;
							$objDB->save();
						}						
					}

					$this->alert_ok();
				}else{
					$this->alert_error();
				}
			}else{
				$this->alert_error();
			}			
		}
	}
?>