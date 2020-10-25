<?php

namespace Home\DAO;

use Home\Common\FIdConst;
use Home\Common\DemoConst;

/**
 * 用户 DAO
 *
 * @author 李静波
 */
class UserDAO extends PSIBaseExDAO
{

  /**
   * 判断某个用户是否被禁用
   *
   * @param string $userId
   *        	用户id
   * @return boolean true: 被禁用
   */
  public function isDisabled($userId)
  {
    $db = $this->db;

    $sql = "select enabled from t_user where id = '%s' ";
    $data = $db->query($sql, $userId);
    if ($data) {
      return $data[0]["enabled"] == 0;
    } else {
      // $userId的用户不存在，也视为被禁用了
      return true;
    }
  }

  /**
   * 判断是否可以登录
   *
   * @param array $params
   * @return string|NULL 可以登录返回用户id，否则返回null
   */
  public function doLogin($params)
  {
    $loginName = $params["loginName"];
    $password = $params["password"];

    $db = $this->db;

    $sql = "select id from t_user where login_name = '%s' and password = '%s' and enabled = 1";

    $data = $db->query($sql, $loginName, md5($password));

    if ($data) {
      return $data[0]["id"];
    } else {
      return null;
    }
  }

  /**
   * 判断当前用户是否有某个功能的权限
   *
   * @param string $userId
   *        	用户id
   * @param string $fid
   *        	功能id
   * @return boolean true:有该功能的权限
   */
  public function hasPermission($userId, $fid)
  {
    $db = $this->db;
    $sql = "select count(*) as cnt
            from  t_role_user ru, t_role_permission rp, 
              (select * from t_permission union select * from t_permission_plus) p
            where ru.user_id = '%s' and ru.role_id = rp.role_id
              and rp.permission_id = p.id and p.fid = '%s' ";
    $data = $db->query($sql, $userId, $fid);

    return $data[0]["cnt"] > 0;
  }

  /**
   * 根据用户id查询用户名称
   *
   * @param string $userId
   *        	用户id
   *        	
   * @return string 用户姓名
   */
  public function getLoginUserName($userId)
  {
    $db = $this->db;

    $sql = "select name from t_user where id = '%s' ";

    $data = $db->query($sql, $userId);

    if ($data) {
      return $data[0]["name"];
    } else {
      return "";
    }
  }

  /**
   * 获得带组织机构的用户全名
   *
   * @param string $userId
   *        	用户id
   * @return string
   */
  public function getLoignUserNameWithOrgFullName($userId)
  {
    $db = $this->db;

    $userName = $this->getLoginUserName($userId);
    if ($userName == "") {
      return $userName;
    }

    $sql = "select o.full_name
            from t_org o, t_user u
            where o.id = u.org_id and u.id = '%s' ";
    $data = $db->query($sql, $userId);
    $orgFullName = "";
    if ($data) {
      $orgFullName = $data[0]["full_name"];
    }

    return addslashes($orgFullName . "\\" . $userName);
  }

  /**
   * 获得用户的登录名
   *
   * @param string $userId
   * @return string
   */
  public function getLoginName($userId)
  {
    $db = $this->db;

    $sql = "select login_name from t_user where id = '%s' ";

    $data = $db->query($sql, $userId);

    if ($data) {
      return $data[0]["login_name"];
    } else {
      return "";
    }
  }

  /**
   * 获得某个组织机构的人员
   */
  public function users($params)
  {
    $db = $this->db;

    $orgId = $params["orgId"];
    $start = $params["start"];
    $limit = $params["limit"];

    $loginName = $params["loginName"];
    $name = $params["name"];
    $enabled = intval($params["enabled"]);

    $sql = "select id, login_name,  name, enabled, org_code, gender, birthday, id_card_number, tel,
              tel02, address, data_org
            from t_user
            where (org_id = '%s') ";
    $queryParam = [];
    $queryParam[] = $orgId;

    if ($loginName) {
      $sql .= " and (login_name like '%s') ";
      $queryParam[] = "%$loginName%";
    }
    if ($name) {
      $sql .= " and (name like '%s' or py like '%s') ";
      $queryParam[] = "%$name%";
      $queryParam[] = "%$name%";
    }
    if ($enabled != -1) {
      $sql .= " and (enabled = %d) ";
      $queryParam[] = $enabled;
    }

    $sql .= " order by org_code
              limit %d , %d ";
    $queryParam[] = $start;
    $queryParam[] = $limit;
    $data = $db->query($sql, $queryParam);

    $result = [];

    foreach ($data as $v) {
      // 查询用户的权限角色
      $userId = $v["id"];
      $sql = "select r.name
              from t_role r, t_role_user u
              where r.id = u.role_id and u.user_id = '%s' 
              order by r.code";
      $d = $db->query($sql, $userId);
      $roleName = "";
      foreach ($d as $index => $r) {
        if ($index > 0) {
          $roleName .= ", ";
        }
        $roleName .= $r["name"];
      }

      $item = [
        "id" => $v["id"],
        "loginName" => $v["login_name"],
        "name" => $v["name"],
        "enabled" => $v["enabled"],
        "orgCode" => $v["org_code"],
        "gender" => $v["gender"],
        "birthday" => $v["birthday"],
        "idCardNumber" => $v["id_card_number"],
        "tel" => $v["tel"],
        "tel02" => $v["tel02"],
        "address" => $v["address"],
        "dataOrg" => $v["data_org"],
        "roleName" => $roleName
      ];
      $result[] = $item;
    }

    $sql = "select count(*) as cnt
            from t_user
            where (org_id = '%s') ";
    $queryParam = [];
    $queryParam[] = $orgId;

    if ($loginName) {
      $sql .= " and (login_name like '%s') ";
      $queryParam[] = "%$loginName%";
    }
    if ($name) {
      $sql .= " and (name like '%s' or py like '%s') ";
      $queryParam[] = "%$name%";
      $queryParam[] = "%$name%";
    }
    if ($enabled != -1) {
      $sql .= " and (enabled = %d) ";
      $queryParam[] = $enabled;
    }

    $data = $db->query($sql, $queryParam);
    $cnt = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $cnt
    ];
  }

  /**
   * 做类似这种增长 '01010001' => '01010002', 用户的数据域+1
   */
  private function incDataOrgForUser($dataOrg)
  {
    $pre = substr($dataOrg, 0, strlen($dataOrg) - 4);
    $seed = intval(substr($dataOrg, -4)) + 1;

    return $pre . str_pad($seed, 4, "0", STR_PAD_LEFT);
  }

  /**
   * 检查数据是否正确
   *
   * @param array $params
   * @return NULL|array 没有错误返回null
   */
  private function checkParams($params)
  {
    $loginName = trim($params["loginName"]);
    $name = trim($params["name"]);
    $orgCode = trim($params["orgCode"]);
    $idCardNumber = trim($params["idCardNumber"]);
    $tel = trim($params["tel"]);
    $tel02 = trim($params["tel02"]);
    $address = trim($params["address"]);

    if ($this->isEmptyStringAfterTrim($loginName)) {
      return $this->bad("登录名不能为空");
    }
    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("姓名不能为空");
    }
    if ($this->isEmptyStringAfterTrim($orgCode)) {
      return $this->bad("编码不能为空");
    }

    if ($this->stringBeyondLimit($loginName, 40)) {
      return $this->bad("登录名长度不能超过40位");
    }
    if ($this->stringBeyondLimit($name, 20)) {
      return $this->bad("姓名长度不能超过20位");
    }
    if ($this->stringBeyondLimit($idCardNumber, 50)) {
      return $this->bad("身份证号长度不能超过50位");
    }
    if ($this->stringBeyondLimit($tel, 50)) {
      return $this->bad("联系电话长度不能超过50位");
    }
    if ($this->stringBeyondLimit($tel02, 50)) {
      return $this->bad("备用电话长度不能超过50位");
    }
    if ($this->stringBeyondLimit($address, 100)) {
      return $this->bad("家庭住址长度不能超过100位");
    }

    return null;
  }

  /**
   * 新增用户
   */
  public function addUser(&$params)
  {
    $db = $this->db;

    $id = $this->newId();
    $loginName = trim($params["loginName"]);
    $name = trim($params["name"]);
    $orgCode = trim($params["orgCode"]);
    $orgId = $params["orgId"];
    $enabled = $params["enabled"];
    $gender = $params["gender"];
    $birthday = $params["birthday"];
    $idCardNumber = trim($params["idCardNumber"]);
    $tel = trim($params["tel"]);
    $tel02 = trim($params["tel02"]);
    $address = trim($params["address"]);

    $py = $params["py"];

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    // 检查登录名是否被使用
    $sql = "select count(*) as cnt from t_user where login_name = '%s' ";
    $data = $db->query($sql, $loginName);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("登录名 [$loginName] 已经存在");
    }

    // 检查组织机构是否存在
    $sql = "select count(*) as cnt from t_org where id = '%s' ";
    $data = $db->query($sql, $orgId);
    $cnt = $data[0]["cnt"];
    if ($cnt != 1) {
      return $this->bad("组织机构不存在");
    }

    // 检查编码是否存在
    $sql = "select count(*) as cnt from t_user where org_code = '%s' ";
    $data = $db->query($sql, $orgCode);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码[$orgCode]已经被其他用户使用");
    }

    // 新增用户的默认密码
    $password = md5("123456");

    // 生成数据域
    $dataOrg = "";
    $sql = "select data_org
            from t_user
            where org_id = '%s'
            order by data_org desc limit 1";
    $data = $db->query($sql, $orgId);
    if ($data) {
      $dataOrg = $this->incDataOrgForUser($data[0]["data_org"]);
    } else {
      $sql = "select data_org from t_org where id = '%s' ";
      $data = $db->query($sql, $orgId);
      if ($data) {
        $dataOrg = $data[0]["data_org"] . "0001";
      } else {
        return $this->bad("组织机构不存在");
      }
    }

    $sql = "insert into t_user (id, login_name, name, org_code, org_id, enabled, password, py,
              gender, birthday, id_card_number, tel, tel02, address, data_org)
            values ('%s', '%s', '%s', '%s', '%s', %d, '%s', '%s',
              '%s', '%s', '%s', '%s', '%s', '%s', '%s') ";
    $rc = $db->execute(
      $sql,
      $id,
      $loginName,
      $name,
      $orgCode,
      $orgId,
      $enabled,
      $password,
      $py,
      $gender,
      $birthday,
      $idCardNumber,
      $tel,
      $tel02,
      $address,
      $dataOrg
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $params["id"] = $id;

    // 操作成功
    return null;
  }

  /**
   * 做类似这种增长 '0101' => '0102'，组织机构的数据域+1
   */
  private function incDataOrg($dataOrg)
  {
    $pre = substr($dataOrg, 0, strlen($dataOrg) - 2);
    $seed = intval(substr($dataOrg, -2)) + 1;

    return $pre . str_pad($seed, 2, "0", STR_PAD_LEFT);
  }

  /**
   * 修改用户
   */
  public function updateUser($params)
  {
    $db = $this->db;

    $id = $params["id"];
    $loginName = trim($params["loginName"]);
    $name = trim($params["name"]);
    $orgCode = trim($params["orgCode"]);
    $orgId = $params["orgId"];
    $enabled = $params["enabled"];
    $gender = $params["gender"];
    $birthday = $params["birthday"];
    $idCardNumber = trim($params["idCardNumber"]);
    $tel = trim($params["tel"]);
    $tel02 = trim($params["tel02"]);
    $address = trim($params["address"]);

    $py = $params["py"];

    $result = $this->checkParams($params);
    if ($result) {
      return $result;
    }

    // 检查登录名是否被使用
    $sql = "select count(*) as cnt from t_user where login_name = '%s' and id <> '%s' ";
    $data = $db->query($sql, $loginName, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("登录名 [$loginName] 已经存在");
    }

    // 检查组织机构是否存在
    $sql = "select count(*) as cnt from t_org where id = '%s' ";
    $data = $db->query($sql, $orgId);
    $cnt = $data[0]["cnt"];
    if ($cnt != 1) {
      return $this->bad("组织机构不存在");
    }

    // 检查编码是否存在
    $sql = "select count(*) as cnt from t_user
            where org_code = '%s' and id <> '%s' ";
    $data = $db->query($sql, $orgCode, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码[$orgCode]已经被其他用户使用");
    }

    $sql = "select org_id, data_org from t_user where id = '%s'";
    $data = $db->query($sql, $id);
    $oldOrgId = $data[0]["org_id"];
    $dataOrg = $data[0]["data_org"];
    if ($oldOrgId != $orgId) {
      // 修改了用户的组织机构， 这个时候要调整数据域
      $sql = "select data_org from t_user
              where org_id = '%s'
              order by data_org desc limit 1";
      $data = $db->query($sql, $orgId);
      if ($data) {
        $dataOrg = $this->incDataOrg($data[0]["data_org"]);
      } else {
        $sql = "select data_org from t_org where id = '%s' ";
        $data = $db->query($sql, $orgId);
        $dataOrg = $data[0]["data_org"] . "0001";
      }
      $sql = "update t_user
              set login_name = '%s', name = '%s', org_code = '%s',
                org_id = '%s', enabled = %d, py = '%s',
                gender = '%s', birthday = '%s', id_card_number = '%s',
                tel = '%s', tel02 = '%s', address = '%s', data_org = '%s'
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $loginName,
        $name,
        $orgCode,
        $orgId,
        $enabled,
        $py,
        $gender,
        $birthday,
        $idCardNumber,
        $tel,
        $tel02,
        $address,
        $dataOrg,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    } else {
      $sql = "update t_user
              set login_name = '%s', name = '%s', org_code = '%s',
                org_id = '%s', enabled = %d, py = '%s',
                gender = '%s', birthday = '%s', id_card_number = '%s',
                tel = '%s', tel02 = '%s', address = '%s'
              where id = '%s' ";
      $rc = $db->execute(
        $sql,
        $loginName,
        $name,
        $orgCode,
        $orgId,
        $enabled,
        $py,
        $gender,
        $birthday,
        $idCardNumber,
        $tel,
        $tel02,
        $address,
        $id
      );
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    return null;
  }

  /**
   * 根据用户id查询用户
   *
   * @param string $id
   * @return array|NULL
   */
  public function getUserById($id)
  {
    $db = $this->db;
    $sql = "select login_name, name from t_user where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return null;
    }

    return array(
      "loginName" => $data[0]["login_name"],
      "name" => $data[0]["name"]
    );
  }

  /**
   * 删除用户
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteUser($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $userName = $params["name"];

    // 判断在采购入库单中是否使用了该用户
    $sql = "select count(*) as cnt from t_pw_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在采购入库单中使用了，不能删除");
    }

    // 判断在销售出库单中是否使用了该用户
    $sql = "select count(*) as cnt from t_ws_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在销售出库单中使用了，不能删除");
    }

    // 判断在销售退货入库单中是否使用了该用户
    $sql = "select count(*) as cnt from t_sr_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在销售退货入库单中使用了，不能删除");
    }

    // 判断在采购退货出库单中是否使用了该用户
    $sql = "select count(*) as cnt from t_pr_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在采购退货出库单中使用了，不能删除");
    }

    // 判断在调拨单中是否使用了该用户
    $sql = "select count(*) as cnt from t_it_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在调拨单中使用了，不能删除");
    }

    // 判断在盘点单中是否使用了该用户
    $sql = "select count(*) as cnt from t_ic_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在盘点单中使用了，不能删除");
    }

    // 判断在收款记录中是否使用了该用户
    $sql = "select count(*) as cnt from t_receiving where rv_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在收款记录中使用了，不能删除");
    }

    // 判断在付款记录中是否使用了该用户
    $sql = "select count(*) as cnt from t_payment where pay_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在盘点单中使用了，不能删除");
    }

    // 判断在采购订单中是否使用了该用户
    $sql = "select count(*) as cnt from t_po_bill where biz_user_id = '%s' or input_user_id = '%s' ";
    $data = $db->query($sql, $id, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("用户[{$userName}]已经在采购订单中使用了，不能删除");
    }

    // TODO 如果增加了其他单据，同样需要做出判断是否使用了该用户

    $sql = "delete from t_role_user where user_id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    $sql = "delete from t_user where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 修改用户登录密码
   *
   * @param array $params
   * @return NULL|array
   */
  public function changePassword($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $password = $params["password"];
    if (strlen($password) < 5) {
      return $this->bad("密码长度不能小于5位");
    }

    $sql = "update t_user
            set password = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, md5($password), $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 修改我的密码
   *
   * @param array $params
   * @return array|NULL
   */
  public function changeMyPassword($params)
  {
    $db = $this->db;

    $userId = $params["userId"];
    $oldPassword = $params["oldPassword"];
    $newPassword = $params["newPassword"];

    // 检验旧密码
    $sql = "select count(*) as cnt from t_user where id = '%s' and password = '%s' ";
    $data = $db->query($sql, $userId, md5($oldPassword));
    $cnt = $data[0]["cnt"];
    if ($cnt != 1) {
      return $this->bad("旧密码不正确");
    }

    if (strlen($newPassword) < 5) {
      return $this->bad("密码长度不能小于5位");
    }

    $sql = "update t_user set password = '%s' where id = '%s' ";
    $rc = $db->execute($sql, md5($newPassword), $userId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 查询数据，用于用户自定义字段
   *
   * @param array $params
   * @return array
   */
  public function queryData($params)
  {
    $db = $this->db;

    $queryKey = $params["queryKey"];
    $loginUserId = $params["loginUserId"];

    if ($queryKey == null) {
      $queryKey = "";
    }

    $sql = "select id, login_name, name from t_user
            where (login_name like '%s' or name like '%s' or py like '%s') ";
    $key = "%{$queryKey}%";
    $queryParams = array();
    $queryParams[] = $key;
    $queryParams[] = $key;
    $queryParams[] = $key;

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL("-8999-02", "t_user", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by login_name
              limit 20";
    $data = $db->query($sql, $queryParams);
    $result = array();
    foreach ($data as $v) {
      $result[] = array(
        "id" => $v["id"],
        "loginName" => $v["login_name"],
        "name" => $v["name"]
      );
    }

    return $result;
  }

  /**
   * 查询用户数据域列表
   *
   * @param array $params
   * @return array
   */
  public function queryUserDataOrg($params)
  {
    $db = $this->db;

    $queryKey = $params["queryKey"];
    $loginUserId = $params["loginUserId"];

    if ($queryKey == null) {
      $queryKey = "";
    }

    $sql = "select id, data_org, name from t_user
            where (login_name like '%s' or name like '%s' or py like '%s' or data_org like '%s') ";
    $key = "%{$queryKey}%";
    $queryParams = array();
    $queryParams[] = $key;
    $queryParams[] = $key;
    $queryParams[] = $key;
    $queryParams[] = $key;

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::WAREHOUSE_EDIT_DATAORG, "t_user", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by data_org
              limit 20";
    $data = $db->query($sql, $queryParams);
    $result = array();
    foreach ($data as $v) {
      $item = array(
        "id" => $v["id"],
        "dataOrg" => $v["data_org"],
        "name" => $v["name"]
      );
      $result[] = $item;
    }
    return $result;
  }

  /**
   * 获得当前登录用户所属公司的Id
   *
   * @param array $params
   * @return string|null
   */
  public function getCompanyId($params)
  {
    $db = $this->db;

    $userId = $params["loginUserId"];

    $result = null;

    if (!$userId) {
      return $result;
    }

    // 获得当前登录用户所属公司的算法：
    // 从最底层的组织机构向上找，直到parent_id为null的那个组织机构就是所属公司

    $sql = "select org_id from t_user where id = '%s' ";
    $data = $db->query($sql, $userId);
    if (!$data) {
      return null;
    }
    $orgId = $data[0]["org_id"];
    $found = false;
    while (!$found) {
      $sql = "select id, parent_id from t_org where id = '%s' ";
      $data = $db->query($sql, $orgId);
      if (!$data) {
        return $result;
      }

      $orgId = $data[0]["parent_id"];

      $result = $data[0]["id"];
      $found = $orgId == null;
    }

    return $result;
  }

  /**
   * 获得登录用户的数据域
   *
   * @param array $params
   * @return string|NULL
   */
  public function getLoginUserDataOrg($params)
  {
    $loginUserId = $params["loginUserId"];

    $db = $this->db;

    $sql = "select data_org from t_user where id = '%s' ";
    $data = $db->query($sql, $loginUserId);

    if ($data) {
      return $data[0]["data_org"];
    } else {
      return null;
    }
  }

  /**
   * 获得当前登录用户的某个功能的数据域
   *
   * @param array $params
   * @return string
   */
  public function getDataOrgForFId($params)
  {
    $fid = $params["fid"];

    $result = array();
    $loginUserId = $params["loginUserId"];

    if ($loginUserId == DemoConst::ADMIN_USER_ID) {
      // admin 是超级管理员
      $result[] = "*";
      return $result;
    }

    $db = $this->db;

    $sql = "select distinct rpd.data_org
            from t_role_permission rp, t_role_permission_dataorg rpd,
              t_role_user ru
            where ru.user_id = '%s' and ru.role_id = rp.role_id
              and rp.role_id = rpd.role_id and rp.permission_id = rpd.permission_id
              and rpd.permission_id = '%s' ";
    $data = $db->query($sql, $loginUserId, $fid);

    foreach ($data as $v) {
      $result[] = $v["data_org"];
    }

    return $result;
  }

  public function userInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $sql = "select login_name, name, org_code, org_id,
              birthday, id_card_number, tel, tel02,
              address, gender, enabled 
            from t_user 
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if (!$data) {
      return $this->emptyResult();
    } else {
      $v = $data[0];

      $sql = "select full_name 
              from t_org
              where id = '%s' ";
      $data = $db->query($sql, $v["org_id"]);
      $orgFullName = $data[0]["full_name"];
      return [
        "loginName" => $v["login_name"],
        "name" => $v["name"],
        "orgCode" => $v["org_code"],
        "orgId" => $v["org_id"],
        "orgFullName" => $orgFullName,
        "birthday" => $v["birthday"],
        "idCardNumber" => $v["id_card_number"],
        "tel" => $v["tel"],
        "tel02" => $v["tel02"],
        "address" => $v["address"],
        "gender" => $v["gender"],
        "enabled" => $v["enabled"]
      ];
    }
  }
}
