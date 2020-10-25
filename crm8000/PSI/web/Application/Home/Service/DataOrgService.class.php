<?php

namespace Home\Service;

/**
 * 数据域Service
 *
 * @author 李静波
 */
class DataOrgService extends PSIBaseService
{

  /**
   * 构建数据域的查询SQL语句
   */
  public function buildSQL($fid, $tableName)
  {
    $queryParams = array();

    $us = new UserService();
    $userDataOrg = $us->getLoginUserDataOrg();

    $dataOrgList = $us->getDataOrgForFId($fid);
    if (count($dataOrgList) == 0) {
      return null; // 全部数据域
    }

    // data_org is null 是为了兼容之前的版本遗留下的数据
    $result = " ( " . $tableName . ".data_org is null or " . $tableName . ".data_org = '' ";
    foreach ($dataOrgList as $dataOrg) {
      if ($dataOrg == "*") {
        return null; // 全部数据域
      }

      if ($dataOrg == "#") {
        $result .= " or " . $tableName . ".data_org = '%s' ";
        $queryParams[] = $userDataOrg;

        continue;
      }

      $result .= " or left(" . $tableName . ".data_org, %d) = '%s' ";
      $queryParams[] = strlen($dataOrg);
      $queryParams[] = $dataOrg;
    }

    $result .= " ) ";

    return array(
      0 => $result,
      1 => $queryParams
    );
  }
}
