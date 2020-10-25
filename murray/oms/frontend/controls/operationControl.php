<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 作业
*/

defined('INPOP') or exit('Access Denied');

class frontend_operationControl extends Frontend{

	//列表
	function listAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			$prototypeInfo = formerService::getPrototypeInfo($id);
			$workflowable = $prototypeInfo['workflowable'];
			$formerInfo = formerService::getInfo($prototypeInfo['formerid']);
			$operations = formerService::getListFromCacheTable($id);
			$operationInfos = array();
			$fields = formerService::getFieldList("prototypeid=".$id);
			$workflows = formerService::getWorkflowList("prototypeid=".$id);
			foreach($operations as $operation){
				$regArray = array();
				$regArray = regService::doList('prototypeid='.$id);
				$operation['reg'] = $regArray;
				if($workflowable > 0){
					//获取操作员
					$uid = $workflows[$operation['workflowid']]['uid'];
					//获取操作组织
					$organizationid = $workflows[$operation['workflowid']]['organizationid'];
					//获取操作角色
					$roleid = $workflows[$operation['workflowid']]['roleid'];
					$thisUid = $this->_user->info['uid'];
					$thisOrganizationidArray = explode(",", $this->_user->info['organizations']);
					$thisRoleidArray = explode(",", $this->_user->info['roleids']);
					//任意三个条件之一满足即可或者已经完结的
					if(($thisUid == $uid) || (in_array($organizationid, $thisOrganizationidArray)) || (in_array($roleid, $thisRoleidArray)) || ($operation['workflowid'] < 0)){
						foreach($fields as $field){
							if($field['bindingid']){
								$bindingid = (int)$field['bindingid'];
								if($bindingid > 0){
									$bindingPrototypeInfo = formerService::getPrototypeInfo($bindingid);
									$infoid = (int)$operation[$field['name']];
									if($infoid > 0){
										$operation[$field['name']] = formerService::getInfoFromCacheTable($bindingid, "id=".$infoid);
									}
								}
							}
						}
						$operationInfos[$operation['id']] = $operation;
					}
				}else{
					foreach($fields as $field){
						if($field['bindingid']){
							$bindingid = (int)$field['bindingid'];
							if($bindingid > 0){
								$bindingPrototypeInfo = formerService::getPrototypeInfo($bindingid);
								$infoid = (int)$operation[$field['name']];
								if($infoid > 0){
									$operation[$field['name']] = formerService::getInfoFromCacheTable($bindingid, "id=".$infoid);
								}
							}
						}
					}
					$operationInfos[$operation['id']] = $operation;				
				}
			}
			$this->view->fields = $fields;
			$this->view->workflows = $workflows;
			$this->view->prototypeid = $id;
			$this->view->prototypeInfo = $prototypeInfo;
			$this->view->formerInfo = $formerInfo;
			$this->view->operationInfos = $operationInfos;
			$this->view->workflowable = $workflowable;
		}
		$this->render();
	}

	//详情
	function showAction(){
		$search = $_GET['s'];
		$sqlArray = explode('|', $search);
		$sql = $sqlArray[0]." = '".$sqlArray[1]."'";
		$dataInfo = array();
		$prototypes = formerService::getPrototypeList();
		$cssArray = array("panel-warning", "panel-info", "panel-success", "panel-primary", "panel-default",  "panel-default", "panel-primary", "panel-success");
		$cssIndex = 0;
		foreach($prototypes as $prototype){
			$dataInfoArray = formerService::getListFromCacheTable($prototype['prototypeid'], $sql);
			$dataFields = formerService::getFieldList("prototypeid=".$prototype['prototypeid']);
			$dataInfo['data'] = $dataInfoArray[0];
			$dataInfo['prototype'] = $prototype;
			$dataInfo['field'] = $dataFields;
			$cssKey = array_rand($cssArray, 1);
			$dataInfo['css'] = $cssArray[$cssIndex];
			$cssIndex ++;
			$dataInfos[$prototype['name']] = $dataInfo;
		}
		$this->view->bianma = $sqlArray[1];
		$this->view->dataInfos = $dataInfos;
		$this->render();
	}

	//添加
	function addAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$operation = $_POST['operation'];
				$operationid = formerService::addCacheTable($id, $operation);
				if($operationid > 0){
					header("location:".SELF_URL."operation/list/?id=".$id);
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$prototypeInfo = formerService::getPrototypeInfo($id);
			$operationInfo = formerService::getListFromCacheTable($id);
			$fields = formerService::getFieldList("prototypeid=".$id);
			$bindingArray = array();
			foreach($fields as $field){
				if($field['bindingid']){
					$bindingid = (int)$field['bindingid'];
					if($bindingid > 0){
						$bindingPrototypeInfo = formerService::getPrototypeInfo($bindingid);
						$bindingArray[$bindingPrototypeInfo['name']] = formerService::getListFromCacheTable($bindingid);
					}
				}
			}
			$this->view->bindingArray = $bindingArray;
			$this->view->fields = $fields;
			$this->view->prototypeid = $id;
			$this->view->prototypeInfo = $prototypeInfo;
			$this->view->operationInfo = $operationInfo;
		}
		$this->render();
	}

	//更新
	function updateAction(){
		$id = (int)$_GET['id'];
		$cacheid = (int)$_GET['cacheid'];
		if(($id > 0) && ($cacheid >0)){
			if($_POST['dosubmit']){
				$operation = $_POST['operation'];
				$operationid = formerService::updateCacheTable($cacheid, $id, $operation);
				if($operationid > 0){
					header("location:".SELF_URL."operation/list/?id=".$id);
					exit;
				}else{
					echo $operationid;
					print_r($_POST);
					exit;
				}
			}
			$prototypeInfo = formerService::getPrototypeInfo($id);
			$operationInfo = formerService::getInfoFromCacheTable($id, "id=".$cacheid);
			$fields = formerService::getFieldList("prototypeid=".$id);
			$bindingArray = array();
			foreach($fields as $field){
				if($field['bindingid']){
					$bindingid = (int)$field['bindingid'];
					if($bindingid > 0){
						$bindingPrototypeInfo = formerService::getPrototypeInfo($bindingid);
						$bindingArray[$bindingPrototypeInfo['name']] = formerService::getListFromCacheTable($bindingid);
					}
				}
			}
			$this->view->fields = $fields;
			$this->view->bindingArray = $bindingArray;
			$this->view->prototypeid = $id;
			$this->view->cacheid = $cacheid;
			$this->view->prototypeInfo = $prototypeInfo;
			$this->view->operationInfo = $operationInfo;
		}
		$this->render();	
	}

	//工作流
	function workflowAction(){
		$id = (int)$_GET['id'];
		$cacheid = (int)$_GET['cacheid'];
		if($id > 0){
			if($cacheid >0){
				if($_POST['dosubmit']){
					$operation = $_POST['operation'];
					$operationid = formerService::updateCacheTable($cacheid, $id, $operation);
					if($operationid > 0){
						header("location:".SELF_URL."operation/list/?id=".$id);
						exit;
					}else{
						echo $operationid;
						print_r($_POST);
						exit;
					}
				}
				echo $id;
				$prototypeInfo = formerService::getPrototypeInfo($id);
				print_r($prototypeInfo);
				$operationInfo = formerService::getInfoFromCacheTable($id, "id=".$cacheid);
				$fieldLists = formerService::getFieldList("prototypeid=".$id);
				print_r($fieldLists);
				$workflowInfo = formerService::getWorkflowInfo($operationInfo['workflowid']);
				$fieldForShow = explode(",", $workflowInfo['fieldshow']);
				$fieldForHidden = explode(",", $workflowInfo['fieldhidden']);
				$bindingArray = array();
				foreach($fieldLists as $field){
					//处理关联字段
					if($field['bindingid']){
						$bindingid = (int)$field['bindingid'];
						if($bindingid > 0){
							$bindingPrototypeInfo = formerService::getPrototypeInfo($bindingid);
							$bindingArray[$bindingPrototypeInfo['name']] = formerService::getListFromCacheTable($bindingid);
						}
					}
					//处理显示字段
					if(in_array($field['fieldid'], $fieldForShow)){
						$field['isshow'] = 1;
					}
					//处理隐藏字段
					if(in_array($field['fieldid'], $fieldForHidden)){
						$field['ishidden'] = 1;
					}
					$fields[$field['fieldid']] = $field;

				}
				$this->view->fields = $fields;
				$this->view->workflowInfo = $workflowInfo;
				$this->view->bindingArray = $bindingArray;
				$this->view->prototypeid = $id;
				$this->view->cacheid = $cacheid;
				$this->view->prototypeInfo = $prototypeInfo;
				$this->view->operationInfo = $operationInfo;
			}else{
				if($_POST['dosubmit']){
					$operation = $_POST['operation'];
					$operationid = formerService::updateCacheTable($cacheid, $id, $operation);
					if($operationid > 0){
						header("location:".SELF_URL."operation/list/?id=".$id);
						exit;
					}else{
						echo $operationid;
						print_r($_POST);
						exit;
					}
				}
				echo $id;
				$prototypeInfo = formerService::getPrototypeInfo($id);
				$operationInfo = formerService::getInfoFromCacheTable($id, "id=".$cacheid);
				$fieldLists = formerService::getFieldList("prototypeid=".$id);
				$workflowInfo = formerService::getWorkflowInfo($operationInfo['workflowid']);
				$fieldForShow = explode(",", $workflowInfo['fieldshow']);
				$fieldForHidden = explode(",", $workflowInfo['fieldhidden']);
				$bindingArray = array();
				foreach($fieldLists as $field){
					//处理关联字段
					if($field['bindingid']){
						$bindingid = (int)$field['bindingid'];
						if($bindingid > 0){
							$bindingPrototypeInfo = formerService::getPrototypeInfo($bindingid);
							$bindingArray[$bindingPrototypeInfo['name']] = formerService::getListFromCacheTable($bindingid);
						}
					}
					//处理显示字段
					if(in_array($field['fieldid'], $fieldForShow)){
						$field['isshow'] = 1;
					}
					echo $field['fieldid'];
					print_r($fieldForShow);
					//处理隐藏字段
					if(in_array($field['fieldid'], $fieldForHidden)){
						$field['ishidden'] = 1;
					}
					echo "++";
					echo $field['fieldid'];
					print_r($fieldForShow);
					$fields[$field['fieldid']] = $field;

				}
				$this->view->fields = $fields;
				$this->view->workflowInfo = $workflowInfo;
				$this->view->bindingArray = $bindingArray;
				$this->view->prototypeid = $id;
				$this->view->cacheid = $cacheid;
				$this->view->prototypeInfo = $prototypeInfo;
				$this->view->operationInfo = $operationInfo;			
			}
		}
		$this->render();	
	}

}

?>