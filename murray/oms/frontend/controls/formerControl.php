<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 模型
*/

defined('INPOP') or exit('Access Denied');

class frontend_formerControl extends Frontend{

	//模型列表
	function listAction(){
		$formerInfos = array();
		$formers = formerService::doList('');
		foreach($formers as $former){
			$formerInfo = formerService::getInfo($former['formerid']);
			$userInfo = $this->_user->getInfoById($former['uid']);
			$formerInfo['username'] = $userInfo['username'];
			$prototypes = formerService::getPrototypeList('formerid='.$former['formerid']);
			$formerInfo['prototypes'] = $prototypes;
			$formerInfos[$former['formerid']] = $formerInfo;
		}
		$this->view->formerInfos = $formerInfos;
		$this->render();
	}

	//模型添加
	function addAction(){
		if($_POST['dosubmit']){
			$former = $_POST['former'];
			$formerid = formerService::doAdd($former);
			if($formerid > 0){
				header("location:".SELF_URL."former/list");
				exit;
			}else{
				print_r($_POST);
				exit;
			}
		}
		$this->render();
	}

	//模型更新
	function updateAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$former = $_POST['former'];
				$formerid = formerService::doUpdate($id, $former);
				if($formerid > 0){
					header("location:".SELF_URL."former/list");
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$formerInfo = formerService::getInfo($id);
			if(empty($formerInfo)) return false;
			$this->view->formerInfo = $formerInfo;
		}
		$this->render();	
	}

	//原型列表
	function listprototypeAction(){
		$id = (int)$_GET['id'];
		$prototypeInfos = array();
		$sql = ($id>0) ? "formerid=".$id : "";
		$prototypes = formerService::getPrototypeList($sql);
		foreach($prototypes as $prototype){
			$prototypeInfo = formerService::getPrototypeInfo($prototype['prototypeid']);
			$prototypeInfos[$prototype['prototypeid']] = $prototypeInfo;
		}
		$this->view->prototypeInfos = $prototypeInfos;
		$this->render();
	}

	//原型添加
	function addprototypeAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$prototype = $_POST['prototype'];
				$prototypeid = formerService::addPrototype($prototype);
				if($prototypeid > 0){
					header("location:".SELF_URL."former/listprototype/?id=".$prototype['formerid']);
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$formers = formerService::doList();
			$this->view->formerid = $id;
			$this->view->formers = $formers;
			$this->render();
		}
	}

	//原型更新
	function updateprototypeAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$prototype = $_POST['prototype'];
				$prototypeid = formerService::updatePrototype($id, $prototype);
				if($prototypeid > 0){
					$prototypeInfo = formerService::getPrototypeInfo($id);
					header("location:".SELF_URL."former/listprototype/?id=".$prototypeInfo['formerid']);
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$prototypeInfo = formerService::getPrototypeInfo($id);
			$formers = formerService::doList();
			if(empty($prototypeInfo)) return false;
			$fieldInfos = array();
			$fields = formerService::getFieldList('prototypeid='.$id);
			foreach($fields as $field){
				$fieldidInfo = formerService::getFieldInfo($field['fieldid']);
				$fieldInfos[$field['fieldid']] = $fieldidInfo;
			}
			$this->view->fieldInfos = $fieldInfos;
			$this->view->formers = $formers;
			$this->view->prototypeInfo = $prototypeInfo;
			$this->render();
		}
	}

	//工作流列表
	function listworkflowAction(){
		$workflowInfos = formerService::getWorkflowList();
		$prototypeInfos = formerService::getPrototypeList();
		$userInfos = $this->_user->getList();
		$organizations = organizationService::doList();
		$aclroles = organizationService::getRoleList();
		$this->view->organizations = $organizations;
		$this->view->aclroles = $aclroles;
		$this->view->workflowInfos = $workflowInfos;
		$this->view->prototypeInfos = $prototypeInfos;
		$this->view->userInfos = $userInfos;
		$this->render();
	}

	//工作流添加
	function addworkflowAction(){
		if($_POST['dosubmit']){
			$workflow = $_POST['workflow'];
			$workflowid = formerService::addWorkflow($workflow);
			if($workflowid > 0){
				header("location:".SELF_URL."former/listworkflow/");
				exit;
			}else{
				print_r($_POST);
				exit;
			}
		}
		$workflowInfos = formerService::getWorkflowList();
		$prototypeInfos = formerService::getPrototypeList();
		$userInfos = $this->_user->getList();
		$organizations = organizationService::doList();
		$aclroles = organizationService::getRoleList();
		$this->view->organizations = $organizations;
		$this->view->aclroles = $aclroles;
		$this->view->workflowInfos = $workflowInfos;
		$this->view->prototypeInfos = $prototypeInfos;
		$this->view->userInfos = $userInfos;
		$this->render();
	}

	//工作流更新
	function updateworkflowAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$workflowArray = $_POST['workflow'];
				$workflow = $workflowArray;
				//处理字段显示
				$fieldshowString = implode(",", $workflowArray['fieldshow']);
				$workflow['fieldshow'] = $fieldshowString;
				//处理字段隐藏
				$fieldhiddenString = implode(",", $workflowArray['fieldhidden']);
				$workflow['fieldhidden'] = $fieldhiddenString;
				$workflowid = formerService::updateWorkflow($id, $workflow);
				if($workflowid > 0){
					header("location:".SELF_URL."former/listworkflow/");
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$workflowInfos = formerService::getWorkflowList();
			$prototypeInfos = formerService::getPrototypeList();
			$userInfos = $this->_user->getList();
			$workflowInfo = formerService::getWorkflowInfo($id);
			$organizations = organizationService::doList();
			$aclroles = organizationService::getRoleList();
			$fieldInfos = formerService::getFieldList('prototypeid='.$workflowInfo['prototypeid']);
			if($workflowInfo['fieldshow']){
				$fieldshowArray = explode(",", $workflowInfo['fieldshow']);
				$workflowInfo['fieldshow'] = $fieldshowArray;
			}
			if($workflowInfo['fieldhidden']){
				$fieldhiddenArray = explode(",", $workflowInfo['fieldhidden']);
				$workflowInfo['fieldhidden'] = $fieldhiddenArray;
			}	
			$this->view->organizations = $organizations;
			$this->view->aclroles = $aclroles;
			$this->view->workflowInfo = $workflowInfo;
			$this->view->workflowInfos = $workflowInfos;
			$this->view->prototypeInfos = $prototypeInfos;
			$this->view->userInfos = $userInfos;
			$this->view->fieldInfos = $fieldInfos;
			$this->render();
		}
	}

	//字段添加
	function addfieldAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$field = $_POST['field'];
				$fieldid = formerService::addField($field);
				if($fieldid > 0){
					header("location:".SELF_URL."former/updateprototype/?id=".$id);
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$prototypes = formerService::getPrototypeList();
			$prototypeInfo = formerService::getPrototypeInfo($id);
			$this->view->prototypeid = $id;
			$this->view->prototypeInfo = $prototypeInfo;
			$this->view->prototypes = $prototypes;
			$this->render();
		}
	}

	//字段更新
	function updatefieldAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$field = $_POST['field'];
				$fieldid = formerService::updateField($id, $field);
				if($fieldid > 0){
					$fieldInfo = formerService::getFieldInfo($id);
					header("location:".SELF_URL."former/updateprototype/?id=".$fieldInfo['prototypeid']);
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$fieldInfo = formerService::getFieldInfo($id);
			if(empty($fieldInfo)) return false;
			$prototypes = formerService::getPrototypeList();
			$prototypeInfo = formerService::getPrototypeInfo($fieldInfo['prototypeid']);
			$this->view->prototypeInfo = $prototypeInfo;
			$this->view->fieldInfo = $fieldInfo;
			$this->view->prototypes = $prototypes;
		}
		$this->render();
	}
}

?>