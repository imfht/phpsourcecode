<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 组织结构
*/

defined('INPOP') or exit('Access Denied');

class frontend_organizationControl extends Frontend{

	//组织结构列表
	function listAction(){
		$organizationInfos = array();
		$organizations = organizationService::doList();
		foreach($organizations as $organization){
			$organizationInfo = organizationService::getInfo($organization['organizationid']);
			$organizationInfo['usernum'] = count(explode(',',$organizationInfo['users']));
			$organizationInfos[$organization['organizationid']] = $organizationInfo;
		}
		$this->view->organizationInfos = $organizationInfos;
		$this->render();
	}

	//组织结构详情
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

	//组织结构添加
	function addAction(){
		if($_POST['dosubmit']){
			$organization = $_POST['organization'];
			$organizationid = organizationService::doAdd($organization);
			if($organizationid > 0){
				header("location:".SELF_URL."organization/list/");
				exit;
			}else{
				print_r($_POST);
				exit;
			}
		}
		$this->render();	
	}

	//组织结构更新
	function updateAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$organization = $_POST['organization'];
				$organizationid = organizationService::doUpdate($id, $organization);
				if($organizationid > 0){
					header("location:".SELF_URL."organization/list/");
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$organizationInfo = organizationService::getInfo($id);
			$this->view->organizationInfo = $organizationInfo;
			$this->render();
		}	
	}

	//人员列表
	function listuserAction(){
		$userInfos = array();
		$users = $this->_user->getList();
		foreach($users as $user){
			$userInfo = $user;
			if($user['organizations'] !== ""){
				$userInfo['organizationArray'] = organizationService::doList(" organizationid in (".$user['organizations'].") ");
			}
			$userInfos[$user['uid']] = $userInfo;
		}
		$this->view->userInfos = $userInfos;
		$this->render();
	}

	//人员添加
	function adduserAction(){
		if($_POST['dosubmit']){
			$userArray = $_POST['user'];
			$user = $userArray;
			if($userArray['organizations']){
				$organizations = implode(",", $userArray['organizations']);
				$user['organizations'] = $organizations;
			}
			if($userArray['roleids']){
				$roleids = implode(",", $userArray['roleids']);
				$user['roleids'] = $roleids;
			}
			$userid = $this->_user->register($user);
			if($userid > 0){
				header("location:".SELF_URL."organization/listuser/");
				exit;
			}else{
				print_r($_POST);
				exit;
			}
		}
		$organizations = organizationService::doList();
		$aclroles = organizationService::getRoleList();
		$this->view->organizations = $organizations;
		$this->view->aclroles = $aclroles;
		$this->render();
	}

	//人员更新
	function updateuserAction(){
		$id = (int)$_GET['id'];
		if($id > 0){
			if($_POST['dosubmit']){
				$userArray = $_POST['user'];
				$user = $userArray;
				if($userArray['organizations']){
					$organizationsString = implode(",", $userArray['organizations']);
					$user['organizations'] = $organizationsString;
				}
				if($userArray['roleids']){
					$roleidsString = implode(",", $userArray['roleids']);
					$user['roleids'] = $roleidsString;
				}
				$userid = $this->_user->doUpdate($id, $user);
				if($userid > 0){
					header("location:".SELF_URL."organization/listuser/");
					exit;
				}else{
					print_r($_POST);
					exit;
				}
			}
			$organizations = organizationService::doList();
			$aclroles = organizationService::getRoleList();
			$userInfo = $this->_user->getInfoById($id);
			if($userInfo['organizations']){
				$organizationArray = explode(",", $userInfo['organizations']);
				$userInfo['organizations'] = $organizationArray;
			}
			if($userInfo['roleids']){
				$roleidArray = explode(",", $userInfo['roleids']);
				$userInfo['roleids'] = $roleidArray;
			}			
			$this->view->organizations = $organizations;
			$this->view->aclroles = $aclroles;
			$this->view->userInfo = $userInfo;
			$this->render();
		}
	}

	//分组列表
	function listgroupAction(){
		$groupInfos = array();
		$groups = groupService::doList();
		foreach($groups as $group){
			$groupInfo = groupService::getInfo($group['groupid']);
			$userInfo = $this->_user->getInfoById($group['uid']);
			$groupInfo['username'] = $userInfo['username'];
			$groupInfos[$group['groupid']] = $groupInfo;
		}
		$this->view->groupInfos = $groupInfos;
		$this->render();
	}

}

?>