<?php

namespace Home\DAO;

use Home\Common\FIdConst;

/**
 * 银行账户 DAO
 *
 * @author 李静波
 */
class BankDAO extends PSIBaseExDAO
{

  /**
   * 某个公司的银行账户
   *
   * @param array $params        	
   */
  public function bankList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];

    $loginUserId = $params["loginUserId"];
    if ($this->loginUserIdNotExists($loginUserId)) {
      return $this->emptyResult();
    }

    $sql = "select b.id, b.bank_name, b.bank_number, b.memo
            from t_bank_account b
            where (b.company_id = '%s') ";

    $ds = new DataOrgDAO($db);
    $queryParams = [];
    $queryParams[] = $companyId;

    $rs = $ds->buildSQL(FIdConst::GL_BANK_ACCOUNT, "b", $loginUserId);
    if ($rs) {
      $sql .= " and " . $rs[0];
      $queryParams = array_merge($queryParams, $rs[1]);
    }

    $sql .= " order by b.bank_name ";

    $result = [];

    $data = $db->query($sql, $queryParams);
    foreach ($data as $v) {
      $result[] = [
        "id" => $v["id"],
        "bankName" => $v["bank_name"],
        "bankNumber" => $v["bank_number"],
        "memo" => $v["memo"]
      ];
    }

    return $result;
  }

  /**
   * 新增银行账户
   *
   * @param array $params        	
   */
  public function addBank(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $dataOrg = $params["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $bankName = $params["bankName"];
    $bankNumber = $params["bankNumber"];
    $memo = $params["memo"];

    // 检查银行账户是否存在
    $sql = "select count(*) as cnt 
            from t_bank_account 
            where company_id = '%s' and bank_name = '%s' and bank_number = '%s' ";
    $data = $db->query($sql, $companyId, $bankName, $bankNumber);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("[{$bankName}-{$bankNumber}]已经存在");
    }

    $id = $this->newId();
    $sql = "insert into t_bank_account(id, bank_name, bank_number, memo,
            date_created, data_org, company_id)
            values ('%s', '%s', '%s', '%s',
            now(), '%s', '%s')";
    $rc = $db->execute($sql, $id, $bankName, $bankNumber, $memo, $dataOrg, $companyId);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["id"] = $id;
    return null;
  }

  public function getBankById($id)
  {
    $db = $this->db;

    $sql = "select bank_name, bank_number from t_bank_account where id = '%s' ";
    $data = $db->query($sql, $id);
    if ($data) {
      return [
        "id" => $id,
        "bankName" => $data[0]["bank_name"],
        "bankNumber" => $data[0]["bank_number"]
      ];
    } else {
      return null;
    }
  }

  /**
   * 编辑银行账户
   *
   * @param array $params        	
   */
  public function updateBank(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];
    if ($this->companyIdNotExists($companyId)) {
      return $this->badParam("companyId");
    }
    $dataOrg = $params["dataOrg"];
    if ($this->dataOrgNotExists($dataOrg)) {
      return $this->badParam("dataOrg");
    }

    $id = $params["id"];
    if (!$this->getBankById($id)) {
      return $this->bad("要编辑的银行账户不存在");
    }

    $bankName = $params["bankName"];
    $bankNumber = $params["bankNumber"];
    $memo = $params["memo"];

    // 检查银行账户是否存在
    $sql = "select count(*) as cnt
            from t_bank_account
            where company_id = '%s' and bank_name = '%s' 
              and bank_number = '%s' and id <> '%s' ";
    $data = $db->query($sql, $companyId, $bankName, $bankNumber, $id);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("[{$bankName}-{$bankNumber}]已经存在");
    }

    $sql = "update t_bank_account
            set bank_name = '%s', bank_number = '%s', memo = '%s'
            where id = '%s' ";
    $rc = $db->execute($sql, $bankName, $bankNumber, $memo, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    return null;
  }

  /**
   * 删除银行账户
   *
   * @param array $params        	
   */
  public function deleteBank(&$params)
  {
    $db = $this->db;

    $id = $params["id"];

    $bank = $this->getBankById($id);
    if (!$bank) {
      return $this->bad("要删除的银行账户不存在");
    }
    $bankName = $bank["bankName"];
    $bankNumber = $bank["bankNumber"];

    // TODO 需要判断银行账户在其他表中是否使用了

    $sql = "delete from t_bank_account where id = '%s' ";
    $rc = $db->execute($sql, $id);
    if ($rc === false) {
      return $this->sqlError(__METHOD__, __LINE__);
    }

    // 操作成功
    $params["bankName"] = $bankName;
    $params["bankNumber"] = $bankNumber;
    return null;
  }
}
