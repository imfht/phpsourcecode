<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 权限 DAO
 *
 * @author 李静波
 */
class PermissionDAO extends PSIBaseExDAO
{

  /**
   * 角色列表
   *
   * @param array $params        	
   * @return array
   */
  public function roleList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];

    // 查询条件
    $loginName = $params["loginName"];
    $name = $params["name"];

    $sql = "select r.id, r.name, r.code 
            from t_role r 
            where (1 = 1) ";
    $queryParams = [];

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PERMISSION_MANAGEMENT, "r", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    if ($loginName) {
      $sql .= " and ( r.id in (
                        select ru.role_id
                        from  t_role_user ru, t_user u
                        where ru.user_id = u.id and u.login_name like '%s') )";
      $queryParams[] = "%$loginName%";
    }
    if ($name) {
      $sql .= " and ( r.id in (
                        select ru.role_id
                        from  t_role_user ru, t_user u
                        where ru.user_id = u.id and (u.name like '%s' or u.py like '%s')) )";
      $queryParams[] = "%$name%";
      $queryParams[] = "%$name%";
    }

    $sql .= "	order by r.code ";
    $data = $db->query($sql, $queryParams);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "code" => $v["code"]
      ];
    }

    return $result;
  }

  /**
   * 某个角色的权限列表
   *
   * @param array $params        	
   * @return array
   */
  public function permissionList($params)
  {
    $db = $this->db;

    $roleId = $params["roleId"];

    $sql = "select p.id, p.name, p.note
            from t_role r, t_role_permission rp, 
              (select * from t_permission union select * from t_permission_plus) p
            where r.id = rp.role_id and r.id = '%s' and rp.permission_id = p.id
            order by convert(p.name USING gbk) collate gbk_chinese_ci";
    $data = $db->query($sql, $roleId);

    $result = [];
    foreach ($data as $v) {
      $pid = $v["id"];
      $item = [
        "id" => $pid,
        "name" => $v["name"],
        "note" => $v["note"]
      ];

      $sql = "select data_org
              from t_role_permission_dataorg
              where role_id = '%s' and permission_id = '%s' ";
      $od = $db->query($sql, $roleId, $pid);
      if ($od) {
        $dataOrg = "";
        foreach ($od as $i => $itemDataOrg) {
          if ($i > 0) {
            $dataOrg .= ";";
          }
          $dataOrg .= $itemDataOrg["data_org"];
        }
        $item["dataOrg"] = $dataOrg;
      } else {
        $item["dataOrg"] = "*";
      }

      $result[] = $item;
    }

    return $result;
  }

  /**
   * 某个角色包含的用户
   *
   * @param array $params        	
   * @return array
   */
  public function userList($params)
  {
    $db = $this->db;

    $roleId = $params["roleId"];

    $sql = "select u.id, u.login_name, u.name, org.full_name
            from t_role r, t_role_user ru, t_user u, t_org org
            where r.id = ru.role_id and r.id = '%s' and ru.user_id = u.id and u.org_id = org.id ";

    $sql .= " order by convert(org.full_name USING gbk) collate gbk_chinese_ci";
    $data = $db->query($sql, $roleId);
    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "orgFullName" => $v["full_name"],
        "loginName" => $v["login_name"]
      ];
    }

    return $result;
  }

  /**
   * 某个权限的数据域列表
   *
   * @param array $params        	
   * @return array
   */
  public function dataOrgList($params)
  {
    $db = $this->db;

    $roleId = $params["roleId"];
    $permissionId = $params["permissionId"];

    $sql = "select data_org
            from t_role_permission_dataorg
            where role_id = '%s' and permission_id = '%s' ";
    $data = $db->query($sql, $roleId, $permissionId);
    $result = [];
    if ($data) {
      foreach ($data as $i => $v) {
        $dataOrg = $v["data_org"];
        $result[$i]["dataOrg"] = $dataOrg;
        if ($dataOrg == "*") {
          $result[$i]["fullName"] = "[全部数据]";
        } else if ($dataOrg == "#") {
          $result[$i]["fullName"] = "[本人数据]";
        } else {
          $fullName = "";
          $sql = "select full_name from t_org where data_org = '%s'";
          $data = $db->query($sql, $dataOrg);
          if ($data) {
            $fullName = $data[0]["full_name"];
          } else {
            $sql = "select o.full_name, u.name
                    from t_org o, t_user u
                    where o.id = u.org_id and u.data_org = '%s' ";
            $data = $db->query($sql, $dataOrg);
            if ($data) {
              $fullName = $data[0]["full_name"] . "\\" . $data[0]["name"];
            }
          }

          $result[$i]["fullName"] = $fullName;
        }
      }
    } else {
      $result[0]["dataOrg"] = "*";
      $result[0]["fullName"] = "[全部数据]";
    }

    return $result;
  }

  /**
   * 获得可以选择的数据域列表
   *
   * @param array $params        	
   * @return array
   */
  public function selectDataOrg($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];

    $result = array();
    $sql = "select full_name, data_org
            from t_org ";
    $queryParams = array();
    $ds = new DataOrgDAO($db);

    $rs = $ds->buildSQL(FIdConst::PERMISSION_MANAGEMENT, "t_org", $loginUserId);
    if ($rs) {
      $sql .= " where " . $rs[0];
      $queryParams = $rs[1];
    }
    $sql .= " order by convert(full_name USING gbk) collate gbk_chinese_ci";

    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i]["fullName"] = $v["full_name"];
      $result[$i]["dataOrg"] = $v["data_org"];
    }

    return $result;
  }

  /**
   * const: 全部权限
   */
  private $ALL_CATEGORY = "[全部]";

  /**
   * 获得权限分类
   */
  public function permissionCategory($params)
  {
    $db = $this->db;

    $queryKey = $params["queryKey"];

    $result = [];

    $result[0]["name"] = $this->ALL_CATEGORY;

    $queryParams = [];
    $sql = "select distinct p.category from (
              select category, py
              from t_permission
              union
              select category, py
              from t_permission_plus ) p 
            where (1 = 1)";
    if ($queryKey) {
      $sql .= " and (p.py like '%s' or p.category like '%s')";
      $queryParams[] = "%{$queryKey}%";
      $queryParams[] = "%{$queryKey}%";
    }
    $sql .= " order by convert(p.category USING gbk) collate gbk_chinese_ci ";
    $data = $db->query($sql, $queryParams);
    foreach ($data as $i => $v) {
      $result[$i + 1]["name"] = $v["category"];
    }

    return $result;
  }

  /**
   * 按权限分类查询权限项
   *
   * @param array $params        	
   * @return array
   */
  public function permissionByCategory($params)
  {
    $db = $this->db;

    $category = $params["category"];

    $sql = "select p.id, p.name, p.note, p.show_order, p.category from (
              select id, name, note, show_order, category
              from t_permission
              union 
              select id, name, note, show_order, category
              from t_permission_plus
              ) p ";

    $queryParams = [];
    if ($category != $this->ALL_CATEGORY) {
      $queryParams[] = $category;

      $sql .= " where p.category = '%s' ";
    }

    if ($category == $this->ALL_CATEGORY) {
      // using gbk : 为了按拼音排序
      $sql .= " order by convert(p.category USING gbk) collate gbk_chinese_ci ";
    } else {
      $sql .= " order by p.show_order";
    }

    $data = $db->query($sql, $queryParams);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "note" => $v["note"]
      ];
    }

    return $result;
  }

  /**
   * 通过id获得角色
   *
   * @param string $id
   *        	角色id
   * @return array
   */
  public function getRoleById($id)
  {
    $db = $this->db;

    $sql = "select name from t_role where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }

    return array(
      "name" => $data[0]["name"]
    );
  }

  /**
   * 删除角色
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function deleteRole($params)
  {
    $db = $this->db;

    // id: 角色id
    $id = $params["id"];

    $role = $this->getRoleById($id);
    if (!$role) {
      return $this->bad("要删除的角色不存在");
    }

    $sql = "delete from t_role_permission_dataorg where role_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_role_permission where role_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_role_user  where role_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_role where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 获得可以选择的权限列表
   *
   * @param array $params        	
   * @return array
   */
  public function selectPermission($params)
  {
    $db = $this->db;

    $idList = $params["idList"];

    $list = explode(",", $idList);
    if (!$list) {
      return array();
    }

    $result = array();

    $sql = "select id, name from t_permission
            order by convert(name USING gbk) collate gbk_chinese_ci";
    $data = $db->query($sql);

    $index = 0;

    foreach ($data as $v) {
      if (!in_array($v["id"], $list)) {
        $result[$index]["id"] = $v["id"];
        $result[$index]["name"] = $v["name"];

        $index++;
      }
    }

    return $result;
  }

  /**
   * 获得可以选择的用户列表
   *
   * @param array $params        	
   * @return array
   */
  public function selectUsers($params)
  {
    $db = $this->db;

    $idList = $params["idList"];

    $loginUserId = $params["loginUserId"];

    $queryKey = $params["queryKey"];

    $list = explode(",", $idList);
    if (!$list) {
      return [];
    }

    $result = [];

    $sql = "select u.id, u.name, u.login_name, o.full_name, u.org_code
            from t_user u, t_org o
            where (u.org_id = o.id) and (u.enabled = 1) ";
    $queryParams = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::PERMISSION_MANAGEMENT, "u", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }

    if (count($list) > 0) {
      $sql .= " and (";
      foreach ($list as $listIndex => $listItem) {
        if ($listIndex > 0) {
          $sql .= " and ";
        }
        $sql .= " u.id <> '%s' ";
        $queryParams[] = $listItem;
      }

      $sql .= ") ";
    }

    if ($queryKey) {
      $sql .= " and (u.org_code like '%s' or u.py like '%s' or u.name like '%s' or u.login_name like '%s') ";
      $queryParams[] = "%{$queryKey}%";
      $queryParams[] = "%{$queryKey}%";
      $queryParams[] = "%{$queryKey}%";
      $queryParams[] = "%{$queryKey}%";
    }

    $sql .= " order by u.org_code";
    $data = $db->query($sql, $queryParams);

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "name" => $v["name"],
        "loginName" => $v["login_name"],
        "orgFullName" => $v["full_name"],
        "code" => $v["org_code"],
      ];
    }

    return $result;
  }

  /**
   * 检查参数
   *
   * @param array $params        	
   * @return array|NULL null: 没有错误
   */
  private function checkParams($params)
  {
    $name = trim($params["name"]);
    $code = trim($params["code"]);

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("角色名称不能为空");
    }

    if ($this->stringBeyondLimit($name, 40)) {
      return $this->bad("角色名称长度不能超过40位");
    }
    if ($this->stringBeyondLimit($code, 40)) {
      return $this->bad("角色编码长度不能超过40位");
    }

    return null;
  }

  /**
   * 新增角色
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function addRole(&$params)
  {
    $db = $this->db;

    $id = $this->newId();
    $name = trim($params["name"]);
    $code = trim($params["code"]);
    $permissionIdList = $params["permissionIdList"];
    $dataOrgList = $params["dataOrgList"];
    $userIdList = $params["userIdList"];

    $loginUserDataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];

    if ($this->dataOrgNotExists($loginUserDataOrg)) {
      return $this->badParam("loginUserDataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    $pid = explode(",", $permissionIdList);
    $doList = explode(",", $dataOrgList);
    $uid = explode(",", $userIdList);

    $sql = "insert into t_role (id, name, data_org, company_id, code)
            values ('%s', '%s', '%s', '%s', '%s') ";
    $rc = $db->execute($sql, $id, $name, $loginUserDataOrg, $companyId, $code);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($pid) {
      foreach ($pid as $i => $v) {
        $sql = "insert into t_role_permission (role_id, permission_id)
                values ('%s', '%s')";
        $rc = $db->execute($sql, $id, $v);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 权限的数据域
        $sql = "delete from t_role_permission_dataorg
                where role_id = '%s' and permission_id = '%s' ";
        $rc = $db->execute($sql, $id, $v);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        $dataOrg = $doList[$i];
        $oList = explode(";", $dataOrg);
        foreach ($oList as $item) {
          if (!$item) {
            continue;
          }

          $sql = "insert into t_role_permission_dataorg(role_id, permission_id, data_org)
                  values ('%s', '%s', '%s')";
          $rc = $db->execute($sql, $id, $v, $item);
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        }
      }
    }

    if ($uid) {
      foreach ($uid as $v) {
        $sql = "insert into t_role_user (role_id, user_id)
                values ('%s', '%s') ";
        $rc = $db->execute($sql, $id, $v);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    $params["id"] = $id;

    // 操作成功
    return null;
  }

  /**
   * 编辑角色
   *
   * @param array $params        	
   * @return NULL|array
   */
  public function modifyRole($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $name = trim($params["name"]);
    $code = trim($params["code"]);
    $permissionIdList = $params["permissionIdList"];
    $dataOrgList = $params["dataOrgList"];
    $userIdList = $params["userIdList"];

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    $pid = explode(",", $permissionIdList);
    $doList = explode(",", $dataOrgList);
    $uid = explode(",", $userIdList);

    $sql = "update t_role 
            set name = '%s', code = '%s' 
            where id = '%s' ";
    $rc = $db->execute($sql, $name, $code, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_role_permission where role_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_role_user where role_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    if ($pid) {
      foreach ($pid as $i => $v) {
        $sql = "insert into t_role_permission (role_id, permission_id)
                values ('%s', '%s')";
        $rc = $db->execute($sql, $id, $v);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        // 权限的数据域
        $sql = "delete from t_role_permission_dataorg
                where role_id = '%s' and permission_id = '%s' ";
        $rc = $db->execute($sql, $id, $v);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }

        $dataOrg = $doList[$i];
        $oList = explode(";", $dataOrg);
        foreach ($oList as $item) {
          if (!$item) {
            continue;
          }

          $sql = "insert into t_role_permission_dataorg(role_id, permission_id, data_org)
                  values ('%s', '%s', '%s')";
          $rc = $db->execute($sql, $id, $v, $item);
          if ($rc === false) {
            return $this->sqlError(__METHOD__, __LINE__);
          }
        }
      }
    }

    if ($uid) {
      foreach ($uid as $v) {
        $sql = "insert into t_role_user (role_id, user_id)
                values ('%s', '%s') ";
        $rc = $db->execute($sql, $id, $v);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    }

    // 操作成功
    return null;
  }
}
