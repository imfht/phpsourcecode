<?php

namespace Home\Service;

use Home\DAO\WarehouseDAO;

/**
 * 基础数据仓库Service
 *
 * @author 李静波
 */
class WarehouseService extends PSIBaseExService
{
  private $LOG_CATEGORY = "基础数据-仓库";

  /**
   * 所有仓库的列表信息
   */
  public function warehouseList()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new WarehouseDAO($this->db());

    return $dao->warehouseList($params);
  }

  /**
   * 新建或编辑仓库
   */
  public function editWarehouse($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];

    $ps = new PinyinService();
    $py = $ps->toPY($name);
    $params["py"] = $py;

    $db = $this->db();

    $db->startTrans();

    $dao = new WarehouseDAO($db);

    $log = null;

    if ($id) {
      // 修改仓库

      $rc = $dao->updateWarehouse($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑仓库：编码 = $code,  名称 = $name";
    } else {
      // 新增仓库

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addWarehouse($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增仓库：编码 = {$code},  名称 = {$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除仓库
   */
  public function deleteWarehouse($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new WarehouseDAO($db);

    $rc = $dao->deleteWarehouse($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "删除仓库： 编码 = {$params['code']}， 名称 = {$params['name']}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  public function queryData($queryKey, $fid)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "loginUserId" => $this->getLoginUserId(),
      "queryKey" => $queryKey
    );

    $dao = new WarehouseDAO($this->db());
    return $dao->queryData($params);
  }

  /**
   * 编辑数据域
   */
  public function editDataOrg($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new WarehouseDAO($db);
    $id = $params["id"];
    $dataOrg = $params["dataOrg"];
    $warehouse = $dao->getWarehouseById($id);
    if (!$warehouse) {
      $db->rollback();
      return $this->bad("仓库不存在");
    }

    $oldDataOrg = $warehouse["dataOrg"];
    $name = $warehouse["name"];

    $rc = $dao->editDataOrg($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $log = "把仓库[{$name}]的数据域从旧值[{$oldDataOrg}]修改为新值[{$dataOrg}]";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }
}
