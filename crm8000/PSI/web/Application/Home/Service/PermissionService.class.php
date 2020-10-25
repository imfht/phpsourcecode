<?php

namespace Home\Service;

use Home\Common\DemoConst;
use Home\DAO\PermissionDAO;

/**
 * 权限 Service
 *
 * @author 李静波
 */
class PermissionService extends PSIBaseExService
{
  private $LOG_CATEGORY = "权限管理";

  public function roleList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new PermissionDAO($this->db());

    return $dao->roleList($params);
  }

  public function permissionList($roleId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "roleId" => $roleId
    );

    $dao = new PermissionDAO($this->db());

    return $dao->permissionList($params);
  }

  public function userList($roleId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "roleId" => $roleId
    );

    $dao = new PermissionDAO($this->db());

    return $dao->userList($params);
  }

  public function editRole($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    if ($this->isDemo() && $id == DemoConst::ADMIN_ROLE_ID) {
      return $this->bad("在演示环境下，系统管理角色不希望被您修改，请见谅");
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new PermissionDAO($db);

    if ($id) {
      // 编辑角色

      $rc = $dao->modifyRole($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑角色[{$name}]";
    } else {
      // 新增角色

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addRole($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增角色[{$name}]";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  public function selectPermission($idList)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "idList" => $idList
    );

    $dao = new PermissionDAO($this->db());

    return $dao->selectPermission($params);
  }

  public function selectUsers($idList, $queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "idList" => $idList,
      "loginUserId" => $this->getLoginUserId(),
      "queryKey" => $queryKey,
    );

    $dao = new PermissionDAO($this->db());

    return $dao->selectUsers($params);
  }

  /**
   * 删除角色
   */
  public function deleteRole($id)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    if ($this->isDemo() && $id == DemoConst::ADMIN_ROLE_ID) {
      return $this->bad("在演示环境下，系统管理角色不希望被您删除，请见谅");
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new PermissionDAO($db);
    $role = $dao->getRoleById($id);

    if (!$role) {
      $db->rollback();
      return $this->bad("要删除的角色不存在");
    }
    $roleName = $role["name"];

    $params = array(
      "id" => $id
    );
    $rc = $dao->deleteRole($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "删除角色[{$roleName}]";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  public function dataOrgList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PermissionDAO($this->db());

    return $dao->dataOrgList($params);
  }

  public function selectDataOrg()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new PermissionDAO($this->db());

    return $dao->selectDataOrg($params);
  }

  /**
   * 获得权限分类
   */
  public function permissionCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PermissionDAO($this->db());

    return $dao->permissionCategory($params);
  }

  /**
   * 按权限分类查询权限项
   */
  public function permissionByCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PermissionDAO($this->db());

    return $dao->permissionByCategory($params);
  }
}
