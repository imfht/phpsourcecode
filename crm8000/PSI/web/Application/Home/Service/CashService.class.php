<?php

namespace Home\Service;

/**
 * 现金Service
 *
 * @author 李静波
 */
class CashService extends PSIBaseService
{

  /**
   * 按日期现金收支列表
   */
  public function cashList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = $params["limit"];

    $dtFrom = $params["dtFrom"];
    $dtTo = $params["dtTo"];

    $db = M();
    $us = new UserService();
    $companyId = $us->getCompanyId();

    $result = array();
    $sql = "select biz_date, in_money, out_money, balance_money
				from t_cash
				where biz_date >= '%s' and biz_date <= '%s'
					and company_id = '%s'
				order by biz_date
				limit %d, %d ";
    $data = $db->query($sql, $dtFrom, $dtTo, $companyId, $start, $limit);
    foreach ($data as $i => $v) {
      $result[$i]["bizDT"] = $this->toYMD($v["biz_date"]);
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
    }

    $sql = "select count(*) as cnt
				from t_cash
				where biz_date >= '%s' and biz_date <= '%s' 
					and company_id = '%s' ";
    $data = $db->query($sql, $dtFrom, $dtTo, $companyId);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }

  /**
   * 某天的现金收支列表
   */
  public function cashDetailList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $start = $params["start"];
    $limit = $params["limit"];

    $bizDT = $params["bizDT"];

    $db = M();
    $us = new UserService();
    $companyId = $us->getCompanyId();

    $result = array();
    $sql = "select biz_date, in_money, out_money, balance_money, date_created,
					ref_type, ref_number
				from t_cash_detail
				where biz_date = '%s' and company_id = '%s'
				order by date_created
				limit %d, %d ";
    $data = $db->query($sql, $bizDT, $companyId, $start, $limit);
    foreach ($data as $i => $v) {
      $result[$i]["bizDT"] = $this->toYMD($v["biz_date"]);
      $result[$i]["inMoney"] = $v["in_money"];
      $result[$i]["outMoney"] = $v["out_money"];
      $result[$i]["balanceMoney"] = $v["balance_money"];
      $result[$i]["dateCreated"] = $v["date_created"];
      $result[$i]["refType"] = $v["ref_type"];
      $result[$i]["refNumber"] = $v["ref_number"];
    }

    $sql = "select count(*) as cnt
				from t_cash_detail
				where biz_date = '%s' and company_id = '%s' ";
    $data = $db->query($sql, $bizDT, $companyId);
    $cnt = $data[0]["cnt"];

    return array(
      "dataList" => $result,
      "totalCount" => $cnt
    );
  }
}
