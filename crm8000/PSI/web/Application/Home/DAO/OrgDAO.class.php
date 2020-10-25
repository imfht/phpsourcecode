<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 组织机构 DAO
 *
 * @author 李静波
 */
class OrgDAO extends PSIBaseExDAO
{

  /**
   * 做类似这种增长 '0101' => '0102'，组织机构的数据域+1
   *
   * @param string $dataOrg
   * @return string
   */
  private function incDataOrg($dataOrg)
  {
    $pre = substr($dataOrg, 0, strlen($dataOrg) - 2);
    $seed = intval(substr($dataOrg, -2)) + 1;

    return $pre . str_pad($seed, 2, "0", STR_PAD_LEFT);
  }

  /**
   * 检查参数是否正确
   *
   * @param array $params
   * @return array|NULL null:没有错误
   */
  private function checkParams($params)
  {
    $name = trim($params["name"]);
    $orgCode = trim($params["orgCode"]);

    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("名称不能为空");
    }

    if ($this->isEmptyStringAfterTrim($orgCode)) {
      return $this->bad("编码不能为空");
    }

    if ($this->stringBeyondLimit($name, 60)) {
      return $this->bad("组织机构名称长度不能超过60");
    }
    if ($this->stringBeyondLimit($orgCode, 20)) {
      return $this->bad("组织机构编码长度不能超过20");
    }

    return null;
  }

  /**
   * 新增组织机构
   *
   * @param array $params
   * @return NULL|array null: 操作成功
   */
  public function addOrg(&$params)
  {
    $db = $this->db;

    $parentId = $params["parentId"];
    $id = $this->newId();
    $name = trim($params["name"]);
    $orgCode = trim($params["orgCode"]);
    $orgType = $params["orgType"];

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    $sql = "select full_name from t_org where id = '%s' ";
    $parentOrg = $db->query($sql, $parentId);
    $fullName = "";
    if (!$parentOrg) {
      $parentId = null;
      $fullName = $name;
    } else {
      $fullName = $parentOrg[0]["full_name"] . "\\" . $name;
    }

    if ($parentId == null) {
      $dataOrg = "01";
      $sql = "select data_org from t_org
              where parent_id is null
              order by data_org desc limit 1";
      $data = $db->query($sql);
      if ($data) {
        $dataOrg = $this->incDataOrg($data[0]["data_org"]);
      }

      $sql = "insert into t_org (id, name, full_name, org_code, parent_id, data_org, org_type)
              values ('%s', '%s', '%s', '%s', null, '%s', %d)";

      $rc = $db->execute($sql, $id, $name, $fullName, $orgCode, $dataOrg, $orgType);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $dataOrg = "";
      $sql = "select data_org from t_org
              where parent_id = '%s'
              order by data_org desc limit 1";
      $data = $db->query($sql, $parentId);
      if ($data) {
        $dataOrg = $this->incDataOrg($data[0]["data_org"]);
      } else {
        $sql = "select data_org from t_org where id = '%s' ";
        $data = $db->query($sql, $parentId);
        if (!$data) {
          return $this->bad("上级组织机构不存在");
        }
        $dataOrg = $data[0]["data_org"] . "01";
      }

      $sql = "insert into t_org (id, name, full_name, org_code, parent_id, data_org, org_type)
              values ('%s', '%s', '%s', '%s', '%s', '%s', %d)";

      $rc = $db->execute($sql, $id, $name, $fullName, $orgCode, $parentId, $dataOrg, $orgType);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    $params["id"] = $id;

    // 操作成功
    return null;
  }

  /**
   * 修改组织机构
   *
   * @param array $params
   * @return NULL|array null：操作成功
   */
  public function updateOrg(&$params)
  {
    $db = $this->db;

    $parentId = $params["parentId"];
    $id = $params["id"];
    $name = trim($params["name"]);
    $orgCode = trim($params["orgCode"]);
    $orgType = $params["orgType"];

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    // 编辑
    if ($parentId == $id) {
      return $this->bad("上级组织不能是自身");
    }
    $fullName = "";

    $sql = "select parent_id from t_org where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->bad("要编辑的组织机构不存在");
    }
    $oldParentId = $data[0]["parent_id"];

    if ($parentId == "root") {
      $parentId = null;
    }

    if ($parentId == null) {
      $fullName = $name;
      $sql = "update t_org
              set name = '%s', full_name = '%s', org_code = '%s', parent_id = null,
                org_type = %d
              where id = '%s' ";
      $rc = $db->execute($sql, $name, $fullName, $orgCode, $orgType, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $tempParentId = $parentId;
      while ($tempParentId != null) {
        $sql = "select parent_id from t_org where id = '%s' ";
        $d = $db->query($sql, $tempParentId);
        if ($d) {
          $tempParentId = $d[0]["parent_id"];

          if ($tempParentId == $id) {
            return $this->bad("不能选择下级组织作为上级组织");
          }
        } else {
          $tempParentId = null;
        }
      }

      $sql = "select full_name from t_org where id = '%s' ";
      $data = $db->query($sql, $parentId);
      if ($data) {
        $parentFullName = $data[0]["full_name"];
        $fullName = $parentFullName . "\\" . $name;

        $sql = "update t_org
                set name = '%s', full_name = '%s', org_code = '%s', parent_id = '%s',
                  org_type = %d
                where id = '%s' ";
        $rc = $db->execute($sql, $name, $fullName, $orgCode, $parentId, $orgType, $id);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        return $this->bad("上级组织不存在");
      }
    }

    if ($oldParentId != $parentId) {
      // 上级组织机构发生了变化，这个时候，需要调整数据域
      $rc = $this->modifyDataOrg($db, $parentId, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 同步下级组织的full_name字段
    $rc = $this->modifyFullName($db, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  private function modifyDataOrg($db, $parentId, $id)
  {
    // 修改自身的数据域
    $dataOrg = "";
    if ($parentId == null) {
      $sql = "select data_org from t_org
              where parent_id is null and id <> '%s'
              order by data_org desc limit 1";
      $data = $db->query($sql, $id);
      if (!$data) {
        $dataOrg = "01";
      } else {
        $dataOrg = $this->incDataOrg($data[0]["data_org"]);
      }
    } else {
      $sql = "select data_org from t_org
              where parent_id = '%s' and id <> '%s'
              order by data_org desc limit 1";
      $data = $db->query($sql, $parentId, $id);
      if ($data) {
        $dataOrg = $this->incDataOrg($data[0]["data_org"]);
      } else {
        $sql = "select data_org from t_org where id = '%s' ";
        $data = $db->query($sql, $parentId);
        $dataOrg = $data[0]["data_org"] . "01";
      }
    }

    $sql = "update t_org
            set data_org = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $dataOrg, $id);
    if ($rc === false) {
      return false;
    }

    // 修改 人员的数据域
    $sql = "select id from t_user
            where org_id = '%s'
            order by org_code ";
    $data = $db->query($sql, $id);
    foreach ($data as $i => $v) {
      $userId = $v["id"];
      $index = str_pad($i + 1, 4, "0", STR_PAD_LEFT);
      $udo = $dataOrg . $index;

      $sql = "update t_user
              set data_org = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $udo, $userId);
      if ($rc === false) {
        return false;
      }
    }

    // 修改下级组织机构的数据域
    $rc = $this->modifySubDataOrg($db, $dataOrg, $id);

    if ($rc === false) {
      return false;
    }

    return true;
  }

  private function modifyFullName($db, $id)
  {
    $sql = "select full_name from t_org where id = '%s' ";
    $data = $db->query($sql, $id);

    if (!$data) {
      return true;
    }

    $fullName = $data[0]["full_name"];

    $sql = "select id, name from t_org where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    foreach ($data as $v) {
      $idChild = $v["id"];
      $nameChild = $v["name"];
      $fullNameChild = $fullName . "\\" . $nameChild;
      $sql = "update t_org set full_name = '%s' where id = '%s' ";
      $rc = $db->execute($sql, $fullNameChild, $idChild);
      if ($rc === false) {
        return false;
      }

      $rc = $this->modifyFullName($db, $idChild); // 递归调用自身
      if ($rc === false) {
        return false;
      }
    }

    return true;
  }

  private function modifySubDataOrg($db, $parentDataOrg, $parentId)
  {
    $sql = "select id from t_org where parent_id = '%s' order by org_code";
    $data = $db->query($sql, $parentId);
    foreach ($data as $i => $v) {
      $subId = $v["id"];

      $next = str_pad($i + 1, 2, "0", STR_PAD_LEFT);
      $dataOrg = $parentDataOrg . $next;
      $sql = "update t_org
              set data_org = '%s'
              where id = '%s' ";
      $db->execute($sql, $dataOrg, $subId);

      // 修改该组织机构的人员的数据域
      $sql = "select id from t_user
              where org_id = '%s'
              order by org_code ";
      $udata = $db->query($sql, $subId);
      foreach ($udata as $j => $u) {
        $userId = $u["id"];
        $index = str_pad($j + 1, 4, "0", STR_PAD_LEFT);
        $udo = $dataOrg . $index;

        $sql = "update t_user
                set data_org = '%s'
                where id = '%s' ";
        $rc = $db->execute($sql, $udo, $userId);
        if ($rc === false) {
          return false;
        }
      }

      $rc = $this->modifySubDataOrg($db, $dataOrg, $subId); // 递归调用自身
      if ($rc === false) {
        return false;
      }
    }

    return true;
  }

  /**
   * 删除组织机构
   *
   * @param string $id
   * @return NULL|array
   */
  public function deleteOrg($id)
  {
    $db = $this->db;

    $sql = "select count(*) as cnt from t_org where parent_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前组织机构还有下级组织，不能删除");
    }

    $sql = "select count(*) as cnt from t_user where org_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前组织机构还有用户，不能删除");
    }

    // 检查当前组织机构在采购订单中是否使用了
    $sql = "select count(*) as cnt from t_po_bill where org_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前组织机构在采购订单中使用了，不能删除");
    }

    // 检查当前组织机构在销售订单中是否使用了
    $sql = "select count(*) as cnt from t_so_bill where org_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前组织机构在销售订单中使用了，不能删除");
    }

    // 检查当前组织机构在是否是仓库核算组织机构
    $sql = "select count(*) as cnt from t_warehouse where org_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("当前组织机构在仓库的核算组织机构中使用了，不能删除");
    }

    $sql = "delete from t_org where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 根据组织机构idc查询组织机构
   *
   * @param string $id
   * @return array|NULL
   */
  public function getOrgById($id)
  {
    $db = $this->db;

    $sql = "select name, org_code from t_org where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }

    return array(
      "name" => $data[0]["name"],
      "orgCode" => $data[0]["org_code"]
    );
  }

  private function orgTypeCodeToName($code)
  {
    switch ($code) {
      case 400:
        return "事业部";
      case 500:
        return "门店";
      case 600:
        return "内部物流组织机构";
      case 700:
        return "办事处";
      case 2000:
        return "客户";
      case 3000:
        return "供应商";
      case 4000:
        return "外协工厂";
      case 5000:
        return "外部物流商";
      default:
        return "";
    }
  }

  /**
   * 所有组织机构
   *
   * @param array $params
   * @return array
   */
  public function allOrgs($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    $enabled = intval($params["enabled"]);

    $ds = new DataOrgDAO($db);
    $queryParams = array();
    $rs = $ds->buildSQL(FIdConst::USR_MANAGEMENT, "t_org", $loginUserId);

    $sql = "select id, name, org_code, full_name, data_org, org_type
            from t_org
            where parent_id is null ";
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = $rs[1];
    }
    $sql .= " order by org_code";

    $orgList1 = $db->query($sql, $queryParams);
    $result = [];

    // 第一级组织
    foreach ($orgList1 as $i => $org1) {
      $result[$i]["id"] = $org1["id"];
      $result[$i]["text"] = $org1["name"];
      $result[$i]["orgCode"] = $org1["org_code"];
      $result[$i]["fullName"] = $org1["full_name"];
      $result[$i]["dataOrg"] = $org1["data_org"];
      $result[$i]["orgType"] = $this->orgTypeCodeToName($org1["org_type"]);

      // 第二级
      $c2 = $this->allOrgsInternal($org1["id"], $db);

      $result[$i]["children"] = $c2;
      $result[$i]["leaf"] = count($c2) == 0;
      $result[$i]["expanded"] = true;
      $result[$i]["iconCls"] = "PSI-org";
    }

    // 统计每个组织机构下的用户数
    for ($i = 0; $i < count($result); $i++) {
      $this->getUserCountWithSubOrg($db, $result[$i], $params);
    }

    // 判断当前状态是否是在查询状态下
    $inQuery = false;
    if ($params["loginName"] || $params["name"] || $enabled != -1) {
      $inQuery = true;
    }

    if ($inQuery) {
      $data = [];

      // 在查询的时候，过滤掉没有用户记录的组织机构
      foreach ($result as $v) {
        $children = $v["children"];
        $newChildren = [];
        foreach ($children as $child) {
          if (intval($child["userCount"]) > 0) {
            $newChildren[] = $child;
          }
        }
        if (count($newChildren) > 0) {
          $v["children"] = $newChildren;
          $data[] = $v;
        } else if (intval($v["userCount"]) > 0) {
          // 下级组织机构没有要查询的用户，但是顶级组织机构(公司)中还有查询的用户
          $data[] = $v;
        }
      }

      return $data;
    } else {
      return $result;
    }
  }

  private function getUserCountWithSubOrg($db, &$org, &$params)
  {
    $loginUserId = $params["loginUserId"];
    $enabled = intval($params["enabled"]);

    // 这里要使用&引用children，这个地方因为少些&导致我浪费好多时间来debug这段代码
    $children = &$org["children"];

    $subCount = 0;
    for ($i = 0; $i < count($children); $i++) {
      $c = $this->getUserCountWithSubOrg($db, $children[$i], $params); // 递归调用自己
      $subCount += $c;
    }

    $sql = "select count(*) as cnt
            from t_user u 
            where (u.org_id = '%s') ";
    $ds = new DataOrgDAO($db);
    $queryParam = [];
    $queryParam[] = $org["id"];
    $rs = $ds->buildSQL(FIdConst::USR_MANAGEMENT, "u", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }
    $loginName = $params["loginName"];
    if ($loginName) {
      $sql .= " and (u.login_name like '%s') ";
      $queryParam[] = "%$loginName%";
    }
    $name = $params["name"];
    if ($name) {
      $sql .= " and (u.name like '%s' or u.py like '%s') ";
      $queryParam[] = "%$name%";
      $queryParam[] = "%$name%";
    }
    if ($enabled != -1) {
      $sql .= " and (u.enabled = %d) ";
      $queryParam[] = $enabled;
    }

    $data = $db->query($sql, $queryParam);
    $cnt = $data[0]["cnt"];
    $totalCount = $subCount + $cnt;

    $org["userCount"] = $totalCount;

    return $totalCount;
  }

  private function allOrgsInternal($parentId, $db)
  {
    $result = [];
    $sql = "select id, name, org_code, full_name, data_org, org_type
            from t_org
            where parent_id = '%s'
            order by org_code";
    $data = $db->query($sql, $parentId);
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["text"] = $v["name"];
      $result[$i]["orgCode"] = $v["org_code"];
      $result[$i]["fullName"] = $v["full_name"];
      $result[$i]["dataOrg"] = $v["data_org"];
      $result[$i]["orgType"] = $this->orgTypeCodeToName($v["org_type"]);

      $c2 = $this->allOrgsInternal($v["id"], $db); // 递归调用自己

      $result[$i]["children"] = $c2;
      $result[$i]["leaf"] = count($c2) == 0;
      $result[$i]["expanded"] = true;
      $result[$i]["iconCls"] = "PSI-org2";
    }

    return $result;
  }

  /**
   * 查询上级组织机构信息
   *
   * @param string $id
   *        	当前组织机构id
   * @return array 上级组织机构
   */
  public function orgParentName($id)
  {
    $db = $this->db;

    $result = array();

    $data = $db->query(
      "select parent_id, name, org_code, org_type from t_org where id = '%s' ",
      $id
    );

    if ($data) {
      $parentId = $data[0]["parent_id"];
      $result["name"] = $data[0]["name"];
      $result["orgCode"] = $data[0]["org_code"];
      $result["parentOrgId"] = $parentId;
      $result["orgType"] = $data[0]["org_type"];

      $data = $db->query("select full_name from t_org where id = '%s' ", $parentId);

      if ($data) {
        $result["parentOrgName"] = $data[0]["full_name"];
      }
    }

    return $result;
  }

  public function orgWithDataOrg($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select id, full_name
            from t_org ";

    $queryParams = array();
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL("-8999-01", "t_org", $loginUserId);
    if ($rs) {
      $sql .= " where " . $rs[0];
      $queryParams = $rs[1];
    }

    $sql .= " order by full_name";

    $data = $db->query($sql, $queryParams);

    $result = array();
    foreach ($data as $i => $v) {
      $result[$i]["id"] = $v["id"];
      $result[$i]["fullName"] = $v["full_name"];
    }

    return $result;
  }

  /**
   * 公司和事业部列表
   *
   * @param array $params
   * @return array
   */
  public function getCompanyExList($params)
  {
    $db = $this->db;

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }
    $fid = $params["fid"];

    $sql = "select g.id, g.org_code, g.full_name, g.org_type
            from t_org g
            where ((g.parent_id is null and (g.org_type is null or g.org_type = 0)) 
              or g.org_type = 400) ";

    $ds = new DataOrgDAO($db);
    $queryParams = [];
    $rs = $ds->buildSQL($fid, "g", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by g.org_code ";

    $result = [];

    $data = $db->query($sql, $queryParams);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["org_code"],
        "name" => $v["full_name"],
        "orgType" => $this->orgTypeCodeToName($v["org_type"])
      ];
    }

    return $result;
  }
}
