<?php
/**
 * 网站后台首页
 * 自动表单管理
 *
 * @author		嬴益虎 <Yingyh@whoneed.com>
 * @copyright	Copyright 2012
 *
 */

	class Auto_formController extends MyAdminController{

		// 初始化
		public function init(){
			parent::init();
		}
		
		// 后台首页
		public function actionIndex(){
			echo 'hello';
		}

		/**
		 *	以下是表字段的相应操作
		 */

		// 查询指定表的所有字段列表
		public function actionTable_fileds(){
			$strTId		= intval(Yii::app()->request->getParam('tid'));

			// 取出系统定义的表名
			$objData	= Whoneed_tables::model()->find("id = '{$strTId}'");

			// 取出实际的表
			if($objData){
				$objModel = ucfirst($objData->physics_name);
				$objData  = new $objModel();

				$data = array();
				$data['objData'] = $objData;
				$data['tid']	 = $strTId;
				$this->display('table_fileds', $data);
			}else{
				$this->alert_error('参数无效，或者此表尚未添加!');
			}
		}

		// 配制指定表的指定字段
		public function actionTable_filed_config(){
			$strTId		= intval(Yii::app()->request->getParam('tid'));	// 表id
			$strFName	= trim(Yii::app()->request->getParam('fname'));	// 字段物理名称
			$objData	= Whoneed_fields::model()->find("table_id = '{$strTId}' and physics_name = '{$strFName}'");

			$data = array();
			$data['objData'] = $objData;
            $data['arrExtraData'] = CF::funGetExtraData($objData->extra_data);
			$data['tid']	 = $strTId;
			$data['fname']	 = $strFName;
			$this->display('table_filed_config', $data);
		}
		
		// 配制指定表的指定字段
		public function actionTable_filed_config_save(){
			// submit
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$strTId		= intval(Yii::app()->request->getParam('tid'));	// 表id
				$strFName	= trim(Yii::app()->request->getParam('fname'));	// 字段物理名称
				$objData	= Whoneed_fields::model()->find("table_id = '{$strTId}' and physics_name = '{$strFName}'");

				if(!$objData){
					$objData = new Whoneed_fields();
					$objData->table_id		= $strTId;
					$objData->physics_name	= $strFName;
				}
				
				unset($_POST['tid']);
				unset($_POST['fname']);

                // deal extra data
                $arrEData = array();
                $arrEData['is_current_data_count'] = intval($_POST['is_current_data_count']);
                $arrEData['current_func']          = trim($_POST['current_func']);
                unset($_POST['is_current_data_count']);
                unset($_POST['current_func']);

                $objData->extra_data = CF::funGetExtraData($arrEData, 'array2string');

				if($_POST){
					foreach($_POST as $k=>$v){
						$objData->$k = $v;
					}
				}				

				if($objData->save()){
					$this->alert_ok();
				}else{
					$this->alert_error();
				}
			}else{
				$this->alert_error();
			}		
		}

		/**
		 *	以下是是根据自动表单自动处理的相应操作
		 */
		
		// 生成自动添加页面
		public function actionAuto_add(){
			// 自动表单中的表id
			$tid	= intval(Yii::app()->request->getParam('tid'));
			$type	= intval(Yii::app()->request->getParam('type'));	//	分类id
			
			// 取得主表：需要自动处理的字段
			$objField = CF::getNeedDealFields($tid);

			// 取得从表：需要自动处理的字段
			$objSlave = CF::getSlaveTable($tid);

			$data = array();
			$data['objField'] = $objField;
			$data['objSlave'] = $objSlave;
			$data['tid']	  = $tid;
			$data['type']	  = $type;
			$this->display('auto_add', $data);			
		}

		// 自动保存自动表单生成的信息
		public function actionAuto_add_save(){
			// step1:参数过滤			
			$tid	= intval(Yii::app()->request->getParam('tid'));	// 自动表单中的表id
			unset($_POST['tid']);

			// step2:处理用户自定义流程
			CF::doUserFlow($tid);

			// step3:主表入库
			$intDId = CF::doAutoSave($tid);
			
			// step3.2:从表入库
			$intSlaveTid	= CF::getSlaveTableId($tid);
			if($intSlaveTid){
				$strFName = CF::getFName($intSlaveTid);
				$_POST[$strFName]['id'] = $intDId;
				$intDId = CF::doAutoSave($intSlaveTid);
			}


			// step4:返回处理结果
			if($intDId){
				$this->alert_ok();
			}else{
				$this->alert_error();
			}		
		}

		// 自动生成编辑页面
		public function actionAuto_edit(){

			$tid	= intval(Yii::app()->request->getParam('tid'));	// 表id
			$rid	= intval(Yii::app()->request->getParam('id'));	// 主表的id

			// 取得主表：需要自动处理的字段
			$objField = CF::getNeedDealFields($tid);

			// 取得从表：需要自动处理的字段
			$objSlave = CF::getSlaveTable($tid);

			// 取得实际的数据
			$objModel = CF::getModelName($tid);
			$objModel = new $objModel();
			$objMData = $objModel->find("id = {$rid}");

			$data = array();
			$data['objMData'] = $objMData;
			$data['objField'] = $objField;
			$data['objSlave'] = $objSlave;
			$data['tid']	  = $tid;
			$data['id']		  = $rid;
			$this->display('auto_edit', $data);			
		}

		// 自动保存自动表单生成的信息
		public function actionAuto_edit_save(){
			// 自动表单中的表id
			$tid	= intval(Yii::app()->request->getParam('tid'));	// 表id
			$id		= intval(Yii::app()->request->getParam('id'));	// 主表的id

			// step2:处理用户自定义流程
			CF::doUserFlow($tid);

			// 有可能在上面用户自定义流程中用到这两个数据
			unset($_POST['tid']);
			unset($_POST['id']);
			
			// step3:自动入库
			$intDId = CF::doAutoSave($tid, $id);

			// step3.2:从表入库
			$intSlaveTid	= CF::getSlaveTableId($tid);
			if($intSlaveTid){
				$strFName = CF::getFName($intSlaveTid);
				$_POST[$strFName]['id'] = $intDId;
				$intDId = CF::doAutoSave($intSlaveTid, $id);
			}

			// step4:返回处理结果
			if($intDId){
				$this->alert_ok();
			}else{
				$this->alert_error();
			}	
		}

		// 自动生成表单列表的信息
		public function actionAuto_list(){

			// 参数
			$tid	= intval(Yii::app()->request->getParam('tid'));			//	表id
			$page	= intval(Yii::app()->request->getParam('pageNum')) - 1;	//	分页
			$type	= intval(Yii::app()->request->getParam('type'));		//	分类id
		
			// 过滤条件
			$cdb = new CDbCriteria;

			// ============== order
			$strOrderBy = '';
			$strOrderBy = CF::getAutoFormOpe($tid, 'orderby');
			if($strOrderBy)
				$cdb->order = $strOrderBy;
			else
				$cdb->order = "id DESC";
			//$cdb->order = "id DESC";

			// ============== where begin
			$arrWhere = array();

			//  分类
			if($type){
				if($type === -1){
					$arrWhere[] = "type = 0";
                }else if($type === -2){
                    // 查询所有子分类，不过滤
                }else{
					$arrWhere[] = "type = {$type}";
				}
			}
			
			// 自定义分类过滤条件
			// 取得自定义的列表操作
			$strTypeParma = '';	// 自定义过滤条件，传递到下一个页面
			$arrAutoFormOpe = array();
			$arrAutoFormOpe = CF::getAutoFormOpe($tid, 'type');
			if($arrAutoFormOpe){				
				foreach($arrAutoFormOpe as $k=>$v){
					$strType = '';
					$strType = Yii::app()->request->getParam($k);
					
					if($v == 'int'){
						$strType = intval($strType);
					}else{
						$strType = trim($strType);
					}
					
					if($strType){
						// -1 查询此类型的顶级
						if($strType === -1){
							$arrWhere[] = "{$k} = 0";
                        }else if($strType === -2){
                            // 查询所有子分类，不过滤
                        }else{
							$arrWhere[] = "{$k} = {$strType}";
						}
						
						// 拼凑需要传递的参数和值
						$strTypeParma .= '/'.$k.'/'.$strType;
					}
				}
			}

			// ============== 自动配制的查询字段
			$arrAjaxPagePost= array();						// ajax 自动分页, post查询参数和值
			$strFName		= CF::getFName($tid);			// 得到post的前缀
			$objQueryField	= CF::getNeedQueryFields($tid);
			if($objQueryField){
				foreach($objQueryField as $Qfield){
					$strField = $Qfield->physics_name;
					
					$strValue = trim($_POST[$strFName][$strField]);
					if($strValue || $strValue === 0 || $strValue !== ''){
						// 解析查询的配制
						$arrQueryDeal = array();
						if($Qfield->query_deal){
							$str = "\$arrQueryDeal = ".$Qfield->query_deal.";";
							@eval($str);
						}

						// 解析配制的查询
						if($arrQueryDeal){
							if($arrQueryDeal['query_type'] == 'like'){				// like '%*%'
								$arrWhere[] = "{$strField} like '%{$strValue}%'";
							}else if($arrQueryDeal['query_type'] == 'left_like'){	// like '***%'								
								$arrWhere[] = "{$strField} like '{$strValue}%'";
							}else if($arrQueryDeal['query_type'] == 'right_like'){	// like '%***'								
								$arrWhere[] = "{$strField} like '%{$strValue}'";
							}else if($arrQueryDeal['query_type'] == 'gt'){			// >								
								$arrWhere[] = "{$strField} > '{$strValue}'";
							}else if($arrQueryDeal['query_type'] == 'lt'){			// <								
								$arrWhere[] = "{$strField} < '{$strValue}'";
							}else if($arrQueryDeal['query_type'] == 'gte'){			// >=								
								$arrWhere[] = "{$strField} >= '{$strValue}'";
							}else if($arrQueryDeal['query_type'] == 'lte'){			// <=								
								$arrWhere[] = "{$strField} <= '{$strValue}'";
							}else if($arrQueryDeal['query_type'] == 'between'){			// between
								$strB = $_POST[$strFName][$strField.'_begin'];
								$strE = $_POST[$strFName][$strField.'_end'];
								if($strB){
									if(empty($strE) || $strE < $strB){
										$strE = $strB;
									}

									$arrWhere[] = "{$strField} between '{$strB}' and '{$strE}' ";
								}								

                                $strTypeParma .= '/'.$strField.'_begin/'.$strB;
                                $strTypeParma .= '/'.$strField.'_end/'.$strE;
							}
						}else{	
							// 普通查询	=
							$arrWhere[] = "{$strField} = '{$strValue}'";
						}

						// 加入到自动Ajax自动分布中
						$arrAjaxPagePost[$strField] = $strValue;
                        $strTypeParma .= '/'.$strField.'/'.$strValue;
					}
				}
			}
			// ============== 自动配制的查询字段 end

			if($arrWhere){
				$strWhere = '';
				$strWhere = implode(' and ', $arrWhere);

				$cdb->condition = $strWhere;
			}
			// =============== where end
            
            // == 自定义查询过滤条件
            $strAFOQuery = '';
            $strAFOQuery = CF::getAutoFormOpe($tid, 'query_condition');
            if($strAFOQuery){
                $arrAFOQuery = array();
                $strDeal = "\$arrAFOQuery = {$strAFOQuery};";
				eval($strDeal); 

                // 自动替换相应过滤条件
                if($arrAFOQuery){
                    foreach($arrAFOQuery as $k=>$v){
                        $cdb->$k = $v;
                    }
                }
            }
            // == 自定义查询过滤条件end

			// 实例化表对象
			$objModel = CF::getModelName($tid);
			$objModel = new $objModel();

			//分页
			$count = $objModel->count($cdb);
			$pages = new CPagination($count);
			$pages->pageSize = self::PAGE_SIZE;
			$pages->currentPage = $page;
			$pages->applyLimit($cdb);

			// 取得列表数据
			$objLData = $objModel->findAll($cdb);

			// 取得当前表需要显示的字段
			$objField = CF::getNeedListFields($tid);

			$data = array();
			$data['pages']			= $pages;
			$data['objLData']		= $objLData;
			$data['objField']		= $objField;
			$data['tid']			= $tid;
			$data['type']			= $type;
			$data['arrAjaxPagePost']= $arrAjaxPagePost;
			$data['strFName']		= $strFName;
			$data['strTypeParma']	= $strTypeParma;
			$this->display('auto_list', $data);	
		}

		// 自动删除
		public function actionAuto_delete(){
			// 自动表单中的表id
			$tid	= intval(Yii::app()->request->getParam('tid'));	// 表id
			$id		= intval(Yii::app()->request->getParam('id'));	// 主表的id

			// 删除主表信息
			$objModel = CF::getModelName($tid);
			$objModel = new $objModel();
			$objDB = $objModel->find("id = {$id}");
			$objDB->delete();

			// 删除从表信息
			$intSlaveTid	= CF::getSlaveTableId($tid);
			if($intSlaveTid){
				$objModel = CF::getModelName($intSlaveTid);
				$objModel = new $objModel();
				$objDB = $objModel->find("id = {$id}");
				$objDB->delete();
			}

			$this->alert_ok();
		}

		// 自动批量删除
		public function actionAuto_batch_delete(){
			// 自动表单中的表id
			$tid	= intval(Yii::app()->request->getParam('tid'));	// 表id
			$ids	= trim(Yii::app()->request->getParam('ids'));	// 主表的id

			if($ids){
				// 删除主表信息
				$objModel = CF::getModelName($tid);
				$objModel = new $objModel();
				$objModel->deleteAll("id in ($ids)");

				// 删除从表信息
				$intSlaveTid	= CF::getSlaveTableId($tid);
				if($intSlaveTid){
					$objModel = CF::getModelName($intSlaveTid);
					$objModel = new $objModel();
					$objModel->deleteAll("id in ($ids)");
				}

				$this->alert_ok();
			}else{
				$this->alert_error('删除失败，请您重新选择需要删除的数据!');
			}
		}

		// 自动生成表单的查询信息
		public function actionAuto_high_search(){
			// 参数
			$tid	= intval(Yii::app()->request->getParam('tid'));		//	表id
			$type	= intval(Yii::app()->request->getParam('type'));	//	分类id
			
			// 取得此表需要查询的字段
			$objField = CF::getNeedQueryFields($tid);

			$data = array();
			$data['tid']		= $tid;
			$data['type']		= $type;
			$data['objField']	= $objField;
			$this->display('auto_high_search', $data);			
		}

		// ================= copy table
		public function actionCopy_table()
		{
			$isOk	= false;
            // XLog::info("in: get=".print_r($_GET, true).', post='.print_r($_POST, true), __FILE__, __FUNCTION__, __LINE__);
            $strIds = trim($_GET['ids']);
            if($strIds)
			{
				$arrId	= explode(',', $strIds);
                // XLog::info('arrId = '.print_r($arrId, true), __FILE__, __FUNCTION__, __LINE__);

				if($arrId && is_array($arrId))
				{
					foreach($arrId as $k => $v)
					{
					    $ret = $this->copy_table_by_id($v);
					    if(!$ret)
						{
                            $isOk	= false;
                            break;
						}else{
                            $isOk	= true;
						}
					}
				}
			}

			if($isOk)
                $this->alert_ok('操作成功', array('callbackType' => true));
            else
                $this->alert_error('Copy fail，请重新操作!');
		}

		public function copy_table_by_id($table_id = 0)
		{
			$isRet	= false;
            //XLog::info("in: table_id = {$table_id}", __FILE__, __FUNCTION__, __LINE__);
            $table_id	= intval($table_id);
            if($table_id)
			{
				// copy whoneed_table
				$objDB = Whoneed_tables::model()->find("id = '{$table_id}'");
				if($objDB)
				{
					$objWTable = new Whoneed_tables();
                    $objWTable->physics_name	= $objDB->physics_name.'[copy]';
                    $objWTable->logic_name		= $objDB->logic_name;
                    $objWTable->slave_tid		= $objDB->slave_tid;
                    $objWTable->remark			= $objDB->remark;
                    $objWTable->operation		= $objDB->operation;
					if($objWTable->save())
					{
                        $new_table_id	= $objWTable->id;
                        //XLog::info("new_table_id = {$new_table_id}", __FILE__, __FUNCTION__, __LINE__);
                        $ret = $this->copy_fields_by_table_id($table_id, $new_table_id);
                        if($ret)
						{
                            $isRet	= true;
						}else{
                            $objWTable->delete();
						}
					}
				}
			}

            return $isRet;
		}

		public function copy_fields_by_table_id($old_talbe_id = 0, $new_table_id = 0)
		{
		    $isRet	= false;
            //XLog::info("in: old_talbe_id = {$old_talbe_id}, new_table_id = {$new_table_id}", __FILE__, __FUNCTION__, __LINE__);
            $old_talbe_id	= intval($old_talbe_id);
            $new_table_id	= intval($new_table_id);
            if($old_talbe_id && $new_table_id)
			{
				$objDB = Whoneed_fields::model()->findAll(" table_id = '{$old_talbe_id}' order by id asc");
				if($objDB)
				{
					foreach($objDB as $obj)
					{
                        //XLog::info("id:".$obj->id, __FILE__, __FUNCTION__, __LINE__);
                        $objNDB = new Whoneed_fields();
                        $objNDB->table_id			= $new_table_id;
                        $objNDB->physics_name		= $obj->physics_name;
                        $objNDB->logic_name			= $obj->logic_name;
                        $objNDB->field_explain		= $obj->field_explain;
                        $objNDB->is_submit			= $obj->is_submit;
                        $objNDB->is_list			= $obj->is_list;
                        $objNDB->is_query			= $obj->is_query;
                        $objNDB->is_after_slave		= $obj->is_after_slave;
                        $objNDB->field_order		= $obj->field_order;
                        $objNDB->field_type			= $obj->field_type;
                        $objNDB->field_type_value	= $obj->field_type_value;
                        $objNDB->flow_deal			= $obj->flow_deal;
                        $objNDB->query_deal			= $obj->query_deal;
                        $objNDB->extra_data			= $obj->extra_data;
						if($objNDB->save())
						{
                            $isRet	= true;
						}else{
                            //XLog::error("save fail!", __FILE__, __FUNCTION__, __LINE__);
						}
					}
				}
			}
            //XLog::info("out. isRet = $isRet", __FILE__, __FUNCTION__, __LINE__);
            return $isRet;
		}
	}
?>