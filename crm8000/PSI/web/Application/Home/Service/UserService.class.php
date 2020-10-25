<?php

namespace Home\Service;

use Home\Common\DemoConst;
use Home\Common\FIdConst;
use Home\DAO\OrgDAO;
use Home\DAO\UserDAO;

/**
 * 用户Service
 *
 * @author 李静波
 */
class UserService extends PSIBaseExService
{
  private $LOG_CATEGORY = "用户管理";

  /**
   * 演示环境中显示在登录窗口上的提示文字
   *
   * @return string
   */
  public function getDemoLoginInfo()
  {
    if ($this->isDemo()) {
      return "当前处于演示环境，请勿保存正式数据，默认的登录名和密码均为 admin <br /><br />如果发现登录失败或者页面样式不正确，请使用<a href='https://browser.360.cn/se/' target='_blank'>360浏览器</a>来访问";
    } else {
      return "";
    }
  }

  /**
   * 判断当前用户是否有$fid对应的权限
   *
   * @param string $fid
   *        	fid
   * @return boolean true：有对应的权限
   */
  public function hasPermission($fid = null)
  {
    if ($fid == FIdConst::PSI_SERVICE) {
      // 停用 购买商业服务 这个菜单功能
      return false;
    }

    $result = session("loginUserId") != null;
    if (!$result) {
      return false;
    }

    $userId = $this->getLoginUserId();

    if ($userId == DemoConst::ADMIN_USER_ID) {
      // admin 用户是超级管理员
      return true;
    }

    // 判断用户是否被禁用
    // 被禁用的用户，视为没有权限
    $ud = new UserDAO($this->db());
    if ($ud->isDisabled($userId)) {
      return false;
    }

    // 修改我的密码，重新登录，首页，使用帮助，关于 这五个功能对所有的在线用户均不需要特别的权限
    $idList = [
      FIdConst::CHANGE_MY_PASSWORD,
      FIdConst::RELOGIN,
      FIdConst::HOME,
      FIdConst::HELP,
      FIdConst::ABOUT
    ];

    if ($fid == null || in_array($fid, $idList)) {
      return $result;
    }

    return $ud->hasPermission($userId, $fid);
  }

  /**
   * 当前登录用户的id
   *
   * @return string|NULL
   */
  public function getLoginUserId()
  {
    return session("loginUserId");
  }

  /**
   * 当前登录用户的姓名
   *
   * @return string
   */
  public function getLoginUserName()
  {
    $dao = new UserDAO($this->db());
    return $dao->getLoginUserName($this->getLoginUserId());
  }

  /**
   * 当前登录用户带组织机构的用户全名
   *
   * @return string
   */
  public function getLoignUserNameWithOrgFullName()
  {
    $dao = new UserDAO($this->db());
    return $dao->getLoignUserNameWithOrgFullName($this->getLoginUserId());
  }

  /**
   * 获得当前登录用户的登录名
   *
   * @return string
   */
  public function getLoginName()
  {
    $dao = new UserDAO($this->db());
    return $dao->getLoginName($this->getLoginUserId());
  }

  /**
   * 登录PSI
   */
  public function doLogin($params)
  {
    $dao = new UserDAO($this->db());
    $loginUserId = $dao->doLogin($params);

    if ($loginUserId) {
      session("loginUserId", $loginUserId);

      $isH5 = $params["isH5"];
      $log = $isH5 == "1" ? "从H5端登录系统 " : "登录系统";

      $bls = new BizlogService();
      $bls->insertBizlog($log);
      return $this->ok();
    } else {
      return $this->bad("用户名或者密码错误");
    }
  }

  public function allOrgs($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new OrgDAO($this->db());

    return $dao->allOrgs($params);
  }

  public function users($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new UserDAO($this->db());
    return $dao->users($params);
  }

  public function editOrg($id, $name, $parentId, $orgCode, $orgType)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    if ($this->isDemo()) {
      if ($id == DemoConst::ORG_COMPANY_ID) {
        return $this->bad("在演示环境下，组织机构[公司]不希望被您修改，请见谅");
      }
      if ($id == DemoConst::ORG_INFODEPT_ID) {
        return $this->bad("在演示环境下，组织机构[信息部]不希望被您修改，请见谅");
      }
    }

    $params = [
      "id" => $id,
      "name" => $name,
      "parentId" => $parentId,
      "orgCode" => $orgCode,
      "orgType" => $orgType
    ];

    $db = $this->db();
    $db->startTrans();

    $log = null;

    $dao = new OrgDAO($db);

    if ($id) {
      $rc = $dao->updateOrg($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }
      $log = "编辑组织机构：名称 = {$name} 编码 = {$orgCode}";
    } else {
      // 新增
      $rc = $dao->addOrg($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增组织机构：名称 = {$name} 编码 = {$orgCode}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  public function orgParentName($id)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new OrgDAO($this->db());
    return $dao->orgParentName($id);
  }

  public function deleteOrg($id)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    if ($this->isDemo()) {
      if ($id == DemoConst::ORG_COMPANY_ID) {
        return $this->bad("在演示环境下，组织机构[公司]不希望被您删除，请见谅");
      }
      if ($id == DemoConst::ORG_INFODEPT_ID) {
        return $this->bad("在演示环境下，组织机构[信息部]不希望被您删除，请见谅");
      }
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new OrgDAO($db);
    $org = $dao->getOrgById($id);
    if (!$org) {
      $db->rollback();
      return $this->bad("要删除的组织机构不存在");
    }
    $name = $org["name"];
    $orgCode = $org["orgCode"];

    $rc = $dao->deleteOrg($id);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "删除组织机构： 名称 = {$name} 编码  = {$orgCode}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 新增或编辑用户
   */
  public function editUser($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $loginName = $params["loginName"];
    $name = $params["name"];
    $orgCode = $params["orgCode"];

    if ($this->isDemo()) {
      if ($id == DemoConst::ADMIN_USER_ID) {
        return $this->bad("在演示环境下，admin用户不希望被您修改，请见谅");
      }
    }

    $pys = new PinyinService();
    $py = $pys->toPY($name);
    $params["py"] = $py;

    $db = $this->db();
    $db->startTrans();

    $dao = new UserDAO($db);

    $log = null;

    if ($id) {
      // 修改

      $rc = $dao->updateUser($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑用户： 登录名 = {$loginName} 姓名 = {$name} 编码 = {$orgCode}";
    } else {
      // 新建

      $rc = $dao->addUser($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新建用户： 登录名 = {$loginName} 姓名 = {$name} 编码 = {$orgCode}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除用户
   */
  public function deleteUser($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    if ($id == DemoConst::ADMIN_USER_ID) {
      return $this->bad("不能删除系统管理员用户");
    }

    // 检查用户是否存在，以及是否能删除
    $db = $this->db();
    $db->startTrans();

    $dao = new UserDAO($db);
    $user = $dao->getUserById($id);

    if (!$user) {
      $db->rollback();
      return $this->bad("要删除的用户不存在");
    }
    $userName = $user["name"];
    $params["name"] = $userName;

    $rc = $dao->deleteUser($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $bs = new BizlogService($db);
    $bs->insertBizlog("删除用户[{$userName}]", $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  public function changePassword($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];

    if ($this->isDemo() && $id == DemoConst::ADMIN_USER_ID) {
      return $this->bad("在演示环境下，admin用户的密码不希望被您修改，请见谅");
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new UserDAO($db);
    $user = $dao->getUserById($id);
    if (!$user) {
      $db->rollback();
      return $this->bad("要修改密码的用户不存在");
    }
    $loginName = $user["loginName"];
    $name = $user["name"];

    $rc = $dao->changePassword($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "修改用户[登录名 ={$loginName} 姓名 = {$name}]的密码";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 清除保存登录用户id的session值
   */
  public function clearLoginUserInSession()
  {
    session("loginUserId", null);
  }

  /**
   * 修改“我的密码”
   */
  public function changeMyPassword($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $userId = $params["userId"];

    if ($this->isDemo() && $userId == DemoConst::ADMIN_USER_ID) {
      return $this->bad("在演示环境下，admin用户的密码不希望被您修改，请见谅");
    }

    if ($userId != $this->getLoginUserId()) {
      return $this->bad("服务器环境发生变化，请重新登录后再操作");
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new UserDAO($db);

    $user = $dao->getUserById($userId);
    if (!$user) {
      return $this->bad("要修改密码的用户不存在");
    }
    $loginName = $user["loginName"];
    $name = $user["name"];

    $rc = $dao->changeMyPassword($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "用户[登录名 ={$loginName} 姓名 = {$name}]修改了自己的登录密码";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, "用户管理");

    $db->commit();

    return $this->ok();
  }

  public function queryData($queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "queryKey" => $queryKey,
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new UserDAO($this->db());
    return $dao->queryData($params);
  }

  /**
   * 判断指定用户id的用户是否存在
   *
   * @return true: 存在
   */
  public function userExists($userId, $db)
  {
    $dao = new UserDAO($db);

    return $dao->getUserById($userId) != null;
  }

  /**
   * 判断指定的组织机构是否存储
   *
   * @return boolean true: 存在
   */
  public function orgExists($orgId, $db)
  {
    $dao = new OrgDAO($db);

    return $dao->getOrgById($orgId) != null;
  }

  /**
   * 获得登录用户的数据域
   */
  public function getLoginUserDataOrg()
  {
    if ($this->isNotOnline()) {
      return null;
    }

    $params = array(
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new UserDAO($this->db());

    return $dao->getLoginUserDataOrg($params);
  }

  /**
   * 获得当前登录用户的某个功能的数据域
   *
   * @param string $fid        	
   */
  public function getDataOrgForFId($fid)
  {
    if ($this->isNotOnline()) {
      return array();
    }

    $params = array(
      "fid" => $fid,
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new UserDAO($this->db());

    return $dao->getDataOrgForFId($params);
  }

  public function orgWithDataOrg()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new OrgDAO($this->db());

    return $dao->orgWithDataOrg($params);
  }

  /**
   * 获得当前登录用户所属公司的Id
   */
  public function getCompanyId()
  {
    $params = array(
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new UserDAO($this->db());

    return $dao->getCompanyId($params);
  }

  /**
   * 查询用户数据域列表
   */
  public function queryUserDataOrg($queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "queryKey" => $queryKey,
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new UserDAO($this->db());

    return $dao->queryUserDataOrg($params);
  }

  public function userInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new UserDAO($this->db());

    return $dao->userInfo($params);
  }
}
