<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 工厂 DAO
 *
 * @author 李静波
 */
class FactoryDAO extends PSIBaseExDAO
{

  /**
   * 供应商分类列表
   *
   * @param array $params
   * @return array
   */
  public function categoryList($params)
  {
    $db = $this->db;

    $code = $params["code"];
    $name = $params["name"];
    $address = $params["address"];
    $contact = $params["contact"];
    $mobile = $params["mobile"];
    $tel = $params["tel"];
    $recordStatus = intval($params["recordStatus"]);

    $inQuery = false;
    if ($code || $name || $address || $contact || $mobile || $tel || $recordStatus != -1) {
      $inQuery = true;
    }

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select c.id, c.code, c.name
            from t_factory_category c ";
    $queryParam = [];
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::FACTORY_CATEGORY, "c", $loginUserId);
    if ($rs) {
      $sql .= " where " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }
    $sql .= " order by c.code";

    $data = $db->query($sql, $queryParam);

    $result = [];
    foreach ($data as $v) {
      $id = $v["id"];

      $queryParam = [];
      $sql = "select count(s.id) as cnt
              from t_factory s
              where (s.category_id = '%s') ";
      $queryParam[] = $id;
      if ($code) {
        $sql .= " and (s.code like '%s') ";
        $queryParam[] = "%{$code}%";
      }
      if ($name) {
        $sql .= " and (s.name like '%s' or s.py like '%s' ) ";
        $queryParam[] = "%{$name}%";
        $queryParam[] = "%{$name}%";
      }
      if ($address) {
        $sql .= " and (s.address like '%s' or s.address_shipping like '%s') ";
        $queryParam[] = "%{$address}%";
        $queryParam[] = "%{$address}%";
      }
      if ($contact) {
        $sql .= " and (s.contact01 like '%s' or s.contact02 like '%s' ) ";
        $queryParam[] = "%{$contact}%";
        $queryParam[] = "%{$contact}%";
      }
      if ($mobile) {
        $sql .= " and (s.mobile01 like '%s' or s.mobile02 like '%s' ) ";
        $queryParam[] = "%{$mobile}%";
        $queryParam[] = "%{$mobile}";
      }
      if ($tel) {
        $sql .= " and (s.tel01 like '%s' or s.tel02 like '%s' ) ";
        $queryParam[] = "%{$tel}%";
        $queryParam[] = "%{$tel}";
      }
      if ($recordStatus != -1) {
        $sql .= " and (s.record_status = %d) ";
        $queryParam[] = $recordStatus;
      }
      $rs = $ds->buildSQL(FIdConst::FACTORY, "s", $loginUserId);
      if ($rs) {
        $sql .= " and " . $rs[0];
        $queryParam = array_merge($queryParam, $rs[1]);
      }

      $d = $db->query($sql, $queryParam);
      $factoryCount = $d[0]["cnt"];

      if ($inQuery && $factoryCount == 0) {
        // 当前是查询，而且当前分类下没有符合查询条件的工厂，就不返回该工厂分类
        continue;
      }

      $result[] = [
        "id" => $id,
        "code" => $v["code"],
        "name" => $v["name"],
        "cnt" => $factoryCount
      ];
    }

    return $result;
  }

  /**
   * 新增工厂分类
   *
   * @param array $params
   * @return array|null
   */
  public function addFactoryCategory(&$params)
  {
    $db = $this->db;

    $code = trim($params["code"]);
    $name = trim($params["name"]);

    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("分类编码不能为空");
    }
    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("分类名称不能为空");
    }

    // 检查分类编码是否已经存在
    $sql = "select count(*) as cnt from t_factory_category where code = '%s' ";
    $data = $db->query($sql, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [$code] 的分类已经存在");
    }

    $id = $this->newId();

    $sql = "insert into t_factory_category (id, code, name, data_org, company_id)
            values ('%s', '%s', '%s', '%s', '%s') ";
    $rc = $db->execute($sql, $id, $code, $name, $dataOrg, $companyId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  /**
   * 编辑工厂分类
   *
   * @param array $params
   * @return array|null
   */
  public function updateFactoryCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = trim($params["code"]);
    $name = trim($params["name"]);

    if ($this->isEmptyStringAfterTrim($code)) {
      return $this->bad("分类编码不能为空");
    }
    if ($this->isEmptyStringAfterTrim($name)) {
      return $this->bad("分类名称不能为空");
    }

    // 检查分类编码是否已经存在
    $sql = "select count(*) as cnt from t_factory_category where code = '%s' and id <> '%s' ";
    $data = $db->query($sql, $code, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [$code] 的分类已经存在");
    }

    $sql = "update t_factory_category
            set code = '%s', name = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $code, $name, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 根据工厂分类id查询工厂分类
   *
   * @param string $id
   * @return array|NULL
   */
  public function getFactoryCategoryById($id)
  {
    $db = $this->db;

    $sql = "select code, name from t_factory_category where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "code" => $data[0]["code"],
        "name" => $data[0]["name"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 删除工厂分类
   *
   * @param array $params
   * @return NULL|array
   */
  public function deleteFactoryCategory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $category = $this->getFactoryCategoryById($id);
    if (!$category) {
      return $this->bad("要删除的分类不存在");
    }

    $params["code"] = $category["code"];
    $params["name"] = $category["name"];
    $name = $params["name"];

    $sql = "select count(*) as cnt
            from t_factory
            where category_id = '%s' ";
    $query = $db->query($sql, $id);
    $cnt = $query[0]["cnt"];
    if ($cnt > 0) {
      $db->rollback();
      return $this->bad("当前分类 [{$name}] 下还有工厂，不能删除");
    }

    $sql = "delete from t_factory_category where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 某个分类下的工厂列表
   *
   * @param array $params
   * @return array
   */
  public function factoryList($params)
  {
    $db = $this->db;

    $categoryId = $params["categoryId"];
    $start = $params["start"];
    $limit = $params["limit"];

    $code = $params["code"];
    $name = $params["name"];
    $address = $params["address"];
    $contact = $params["contact"];
    $mobile = $params["mobile"];
    $tel = $params["tel"];
    $recordStatus = intval($params["recordStatus"]);

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select id, category_id, code, name, contact01, tel01, mobile01,
              contact02, tel02, mobile02, init_payables, init_payables_dt,
              address, bank_name, bank_account, tax_number, fax, note, 
              data_org, record_status
            from t_factory
            where (category_id = '%s')";
    $queryParam = [];
    $queryParam[] = $categoryId;
    if ($code) {
      $sql .= " and (code like '%s' ) ";
      $queryParam[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (name like '%s' or py like '%s' ) ";
      $queryParam[] = "%{$name}%";
      $queryParam[] = "%{$name}%";
    }
    if ($address) {
      $sql .= " and (address like '%s' or address_shipping like '%s') ";
      $queryParam[] = "%$address%";
      $queryParam[] = "%$address%";
    }
    if ($contact) {
      $sql .= " and (contact01 like '%s' or contact02 like '%s' ) ";
      $queryParam[] = "%{$contact}%";
      $queryParam[] = "%{$contact}%";
    }
    if ($mobile) {
      $sql .= " and (mobile01 like '%s' or mobile02 like '%s' ) ";
      $queryParam[] = "%{$mobile}%";
      $queryParam[] = "%{$mobile}";
    }
    if ($tel) {
      $sql .= " and (tel01 like '%s' or tel02 like '%s' ) ";
      $queryParam[] = "%{$tel}%";
      $queryParam[] = "%{$tel}";
    }
    if ($recordStatus != -1) {
      $sql .= " and (record_status = %d) ";
      $queryParam[] = $recordStatus;
    }

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::FACTORY, "t_factory", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }

    $queryParam[] = $start;
    $queryParam[] = $limit;
    $sql .= " order by code
              limit %d, %d";
    $result = [];
    $data = $db->query($sql, $queryParam);
    foreach ($data as $v) {
      $initDT = $v["init_payables_dt"] ? $this->toYMD($v["init_payables_dt"]) : null;

      $result[] = [
        "id" => $v["id"],
        "categoryId" => $v["category_id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "address" => $v["address"],
        "contact01" => $v["contact01"],
        "tel01" => $v["tel01"],
        "mobile01" => $v["mobile01"],
        "contact02" => $v["contact02"],
        "tel02" => $v["tel02"],
        "mobile02" => $v["mobile02"],
        "initPayables" => $v["init_payables"],
        "initPayablesDT" => $initDT,
        "bankName" => $v["bank_name"],
        "bankAccount" => $v["bank_account"],
        "tax" => $v["tax_number"],
        "fax" => $v["fax"],
        "note" => $v["note"],
        "dataOrg" => $v["data_org"],
        "recordStatus" => $v["record_status"]
      ];
    }

    $sql = "select count(*) as cnt from t_factory where (category_id  = '%s') ";
    $queryParam = [];
    $queryParam[] = $categoryId;
    if ($code) {
      $sql .= " and (code like '%s' ) ";
      $queryParam[] = "%{$code}%";
    }
    if ($name) {
      $sql .= " and (name like '%s' or py like '%s' ) ";
      $queryParam[] = "%{$name}%";
      $queryParam[] = "%{$name}%";
    }
    if ($address) {
      $sql .= " and (address like '%s') ";
      $queryParam[] = "%$address%";
    }
    if ($contact) {
      $sql .= " and (contact01 like '%s' or contact02 like '%s' ) ";
      $queryParam[] = "%{$contact}%";
      $queryParam[] = "%{$contact}%";
    }
    if ($mobile) {
      $sql .= " and (mobile01 like '%s' or mobile02 like '%s' ) ";
      $queryParam[] = "%{$mobile}%";
      $queryParam[] = "%{$mobile}";
    }
    if ($tel) {
      $sql .= " and (tel01 like '%s' or tel02 like '%s' ) ";
      $queryParam[] = "%{$tel}%";
      $queryParam[] = "%{$tel}";
    }
    if ($recordStatus != -1) {
      $sql .= " and (record_status = %d) ";
      $queryParam[] = $recordStatus;
    }
    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::FACTORY, "t_factory", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParam = array_merge($queryParam, $rs[1]);
    }
    $data = $db->query($sql, $queryParam);
    $cnt = $data[0]["cnt"];

    return [
      "dataList" => $result,
      "totalCount" => $cnt
    ];
  }

  public function getFactoryById($id)
  {
    $db = $this->db;

    $sql = "select code, name from t_factory where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "id" => $id,
        "code" => $data[0]["code"],
        "name" => $data[0]["name"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 新建工厂
   *
   * @param array $params
   * @return NULL|array
   */
  public function addFactory(&$params)
  {
    $db = $this->db;

    $code = $params["code"];
    $name = $params["name"];
    $address = $params["address"];
    $contact01 = $params["contact01"];
    $mobile01 = $params["mobile01"];
    $tel01 = $params["tel01"];
    $contact02 = $params["contact02"];
    $mobile02 = $params["mobile02"];
    $tel02 = $params["tel02"];
    $bankName = $params["bankName"];
    $bankAccount = $params["bankAccount"];
    $tax = $params["tax"];
    $fax = $params["fax"];
    $note = $params["note"];
    $recordStatus = $params["recordStatus"];

    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $categoryId = $params["categoryId"];
    $py = $params["py"];

    // 检查编码是否已经存在
    $sql = "select count(*) as cnt from t_factory where code = '%s' ";
    $data = $db->query($sql, $code);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [$code] 的工厂已经存在");
    }

    $id = $this->newId();
    $params["id"] = $id;

    $sql = "insert into t_factory (id, category_id, code, name, py, 
              contact01, tel01, mobile01, contact02, tel02, mobile02, 
              address, bank_name, bank_account, tax_number, fax, note, 
              data_org, company_id, record_status)
            values ('%s', '%s', '%s', '%s', '%s', 
              '%s', '%s', '%s', '%s', '%s', '%s',
              '%s', '%s', '%s', '%s', '%s', '%s', 
              '%s', '%s', %d)  ";
    $rc = $db->execute(
      $sql,
      $id,
      $categoryId,
      $code,
      $name,
      $py,
      $contact01,
      $tel01,
      $mobile01,
      $contact02,
      $tel02,
      $mobile02,
      $address,
      $bankName,
      $bankAccount,
      $tax,
      $fax,
      $note,
      $dataOrg,
      $companyId,
      $recordStatus
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 编辑工厂
   *
   * @param array $params
   * @return NULL|array
   */
  public function updateFactory(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];
    $address = $params["address"];
    $contact01 = $params["contact01"];
    $mobile01 = $params["mobile01"];
    $tel01 = $params["tel01"];
    $contact02 = $params["contact02"];
    $mobile02 = $params["mobile02"];
    $tel02 = $params["tel02"];
    $bankName = $params["bankName"];
    $bankAccount = $params["bankAccount"];
    $tax = $params["tax"];
    $fax = $params["fax"];
    $note = $params["note"];
    $recordStatus = $params["recordStatus"];

    $categoryId = $params["categoryId"];
    $py = $params["py"];

    // 检查编码是否已经存在
    $sql = "select count(*) as cnt from t_factory where code = '%s'  and id <> '%s' ";
    $data = $db->query($sql, $code, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("编码为 [$code] 的工厂已经存在");
    }

    $sql = "update t_factory
            set code = '%s', name = '%s', category_id = '%s', py = '%s',
              contact01 = '%s', tel01 = '%s', mobile01 = '%s',
              contact02 = '%s', tel02 = '%s', mobile02 = '%s',
              address = '%s',
              bank_name = '%s', bank_account = '%s', tax_number = '%s',
              fax = '%s', note = '%s', record_status = %d
            where id = '%s'  ";

    $rc = $db->execute(
      $sql,
      $code,
      $name,
      $categoryId,
      $py,
      $contact01,
      $tel01,
      $mobile01,
      $contact02,
      $tel02,
      $mobile02,
      $address,
      $bankName,
      $bankAccount,
      $tax,
      $fax,
      $note,
      $recordStatus,
      $id
    );
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 初始化应付账款
   *
   * @param array $params
   * @return NULL|array
   */
  public function initPayables(&$params)
  {
    $db = $this->db;

    $id = $params["id"];
    $initPayables = $params["initPayables"];
    $initPayablesDT = $params["initPayablesDT"];

    $dataOrg = $params["dataOrg"];
    $companyId = $params["companyId"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }

    $sql = "select count(*) as cnt
            from t_payables_detail
            where ca_id = '%s' and ca_type = 'factory' and ref_type <> '应付账款期初建账'
              and company_id = '%s' ";
    $data = $db->query($sql, $id, $companyId);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      // 已经有往来业务发生，就不能修改应付账了
      return null;
    }

    $initPayables = floatval($initPayables);
    if ($initPayables && $initPayablesDT) {
      $sql = "update t_factory
              set init_payables = %f, init_payables_dt = '%s'
              where id = '%s' ";
      $rc = $db->execute($sql, $initPayables, $initPayablesDT, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 应付明细账
      $sql = "select id from t_payables_detail
              where ca_id = '%s' and ca_type = 'factory' and ref_type = '应付账款期初建账'
                and company_id = '%s' ";
      $data = $db->query($sql, $id, $companyId);
      if ($data) {
        $payId = $data[0]["id"];
        $sql = "update t_payables_detail
                set pay_money = %f ,  balance_money = %f , biz_date = '%s', date_created = now(), act_money = 0
                where id = '%s' ";
        $rc = $db->execute($sql, $initPayables, $initPayables, $initPayablesDT, $payId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $payId = $this->newId();
        $sql = "insert into t_payables_detail (id, pay_money, act_money, balance_money, ca_id,
                  ca_type, ref_type, ref_number, biz_date, date_created, data_org, company_id)
                values ('%s', %f, 0, %f, '%s', 'factory', '应付账款期初建账', '%s', '%s', now(), '%s', '%s') ";
        $rc = $db->execute(
          $sql,
          $payId,
          $initPayables,
          $initPayables,
          $id,
          $id,
          $initPayablesDT,
          $dataOrg,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }

      // 应付总账
      $sql = "select id from t_payables
              where ca_id = '%s' and ca_type = 'factory'
                and company_id = '%s' ";
      $data = $db->query($sql, $id, $companyId);
      if ($data) {
        $pId = $data[0]["id"];
        $sql = "update t_payables
                set pay_money = %f ,  balance_money = %f , act_money = 0
                where id = '%s' ";
        $rc = $db->execute($sql, $initPayables, $initPayables, $pId);
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      } else {
        $pId = $this->newId();
        $sql = "insert into t_payables (id, pay_money, act_money, balance_money, ca_id,
                  ca_type, data_org, company_id)
                values ('%s', %f, 0, %f, '%s', 'factory', '%s', '%s') ";
        $rc = $db->execute(
          $sql,
          $pId,
          $initPayables,
          $initPayables,
          $id,
          $dataOrg,
          $companyId
        );
        if ($rc === false) {
          return $this->sqlError(__METHOD__, __LINE__);
        }
      }
    } else {
      // 清除应付账款初始化数据
      $sql = "update t_factory
              set init_payables = null, init_payables_dt = null
              where id = '%s' ";
      $rc = $db->execute($sql, $id);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 明细账
      $sql = "delete from t_payables_detail
              where ca_id = '%s' and ca_type = 'factory' and ref_type = '应付账款期初建账'
                and company_id = '%s' ";
      $rc = $db->execute($sql, $id, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }

      // 总账
      $sql = "delete from t_payables
              where ca_id = '%s' and ca_type = 'factory'
                and company_id = '%s' ";
      $rc = $db->execute($sql, $id, $companyId);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    return null;
  }

  /**
   * 获得某个工厂的详情
   *
   * @param array $params
   * @return array
   */
  public function factoryInfo($params)
  {
    $db = $this->db;

    $id = $params["id"];

    $result = [];

    $sql = "select category_id, code, name, contact01, mobile01, tel01,
              contact02, mobile02, tel02, address,
              init_payables, init_payables_dt,
              bank_name, bank_account, tax_number, fax, note,
              record_status
            from t_factory
            where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      $result["categoryId"] = $data[0]["category_id"];
      $result["code"] = $data[0]["code"];
      $result["name"] = $data[0]["name"];
      $result["contact01"] = $data[0]["contact01"];
      $result["mobile01"] = $data[0]["mobile01"];
      $result["tel01"] = $data[0]["tel01"];
      $result["contact02"] = $data[0]["contact02"];
      $result["mobile02"] = $data[0]["mobile02"];
      $result["tel02"] = $data[0]["tel02"];
      $result["address"] = $data[0]["address"];
      $result["initPayables"] = $data[0]["init_payables"];
      $d = $data[0]["init_payables_dt"];
      if ($d) {
        $result["initPayablesDT"] = $this->toYMD($d);
      }
      $result["bankName"] = $data[0]["bank_name"];
      $result["bankAccount"] = $data[0]["bank_account"];
      $result["tax"] = $data[0]["tax_number"];
      $result["fax"] = $data[0]["fax"];
      $result["note"] = $data[0]["note"];
      $result["recordStatus"] = $data[0]["record_status"];
    }

    return $result;
  }

  /**
   * 删除工厂
   *
   * @param array $params
   */
  public function deleteFactory(&$params)
  {
    $db = $this->db;

    // 工厂id
    $id = $params["id"];

    $factory = $this->getFactoryById($id);
    if (!$factory) {
      return $this->bad("要删除的工厂不存在");
    }

    $code = $factory["code"];
    $name = $factory["name"];

    // 检查工厂在成品委托生产订单中是否使用过
    $sql = "select count(*) as cnt from t_dmo_bill where factory_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("工厂[{$name}]已经在成品委托生产订单中使用了，不能再删除");
    }

    // 检查工厂在成品委托生产入库单中是否使用过
    $sql = "select count(*) as cnt from t_dmw_bill where factory_id = '%s' ";
    $data = $db->query($sql, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("工厂[{$name}]已经在成品委托生产入库单中使用了，不能再删除");
    }

    // 删除
    $sql = "delete from t_factory where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除应付总账
    $sql = "delete from t_payables where ca_id = '%s' and ca_type = 'factory' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 删除应付明细账
    $sql = "delete from t_payables_detail where ca_id = '%s' and ca_type = 'factory' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["code"] = $code;
    $params["name"] = $name;
    return null;
  }

  /**
   * 工厂自定义字段， 查询数据
   *
   * @param array $params
   * @return array
   */
  public function queryData($params)
  {
    $db = $this->db;

    $queryKey = $params["queryKey"];
    $loginUserId = $params["loginUserId"];

    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    if ($queryKey == null) {
      $queryKey = "";
    }

    $sql = "select id, code, name, tel01, fax, contact01
            from t_factory
            where (record_status = 1000)
              and (code like '%s' or name like '%s' or py like '%s') ";
    $queryParams = array();
    $key = "%{$queryKey}%";
    $queryParams[] = $key;
    $queryParams[] = $key;
    $queryParams[] = $key;

    $ds = new DataOrgDAO($db);
    $rs = $ds->buildSQL(FIdConst::FACTORY_BILL, "t_factory", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by code
              limit 20";
    $data = $db->query($sql, $queryParams);

    $result = [];

    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "code" => $v["code"],
        "name" => $v["name"],
        "tel01" => $v["tel01"],
        "fax" => $v["fax"],
        "contact01" => $v["contact01"]
      ];
    }

    return $result;
  }
}
