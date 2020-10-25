<?php

namespace Home\DAO;

/**
 * 会计期间 DAO
 *
 * @author 李静波
 */
class GLPeriodDAO extends PSIBaseExDAO
{

  /**
   * 某个公司的全部会计期间
   */
  public function periodList($params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];

    $sql = "select acc_year, acc_month, acc_gl_kept, acc_gl_closed,
              acc_detail_kept, acc_detail_closed, period_closed, year_forward
            from t_acc_period
            where company_id = '%s' 
            order by acc_year desc, acc_month asc";
    $data = $db->query($sql, $companyId);

    $result = [];
    $mark = "√";
    foreach ($data as $v) {
      $result[] = [
        "year" => $v["acc_year"],
        "month" => $v["acc_month"],
        "glKept" => $v["acc_gl_kept"] == 1 ? $mark : null,
        "glClosed" => $v["acc_gl_closed"] == 1 ? $mark : null,
        "detailKept" => $v["acc_detail_kept"] == 1 ? $mark : null,
        "detailClosed" => $v["acc_detail_closed"] == 1 ? $mark : null,
        "periodClosed" => $v["period_closed"] == 1 ? $mark : null,
        "yearForward" => $v["year_forward"] == 1 ? $mark : null
      ];
    }

    return $result;
  }

  /**
   * 初始化某个公司的本年度会计期间
   */
  public function initPeriod(&$params)
  {
    $db = $this->db;

    $companyId = $params["companyId"];

    $sql = "select name from t_org where id = '%s' and parent_id is null ";
    $data = $db->query($sql, $companyId);
    if (!$data) {
      return $this->badParam("companyId");
    }
    $name = $data[0]["name"];

    $year = date("Y");

    $sql = "select count(*) as cnt from t_acc_period
            where company_id = '%s' and acc_year = %d ";
    $data = $db->query($sql, $companyId, $year);
    $cnt = $data[0]["cnt"];
    if ($cnt > 0) {
      return $this->bad("[{$name}]{$year}年的会计期间已经初始化过了");
    }

    for ($month = 1; $month < 13; $month++) {
      $sql = "insert into t_acc_period(id, company_id, acc_year,acc_month,
              acc_gl_kept, acc_gl_closed, acc_detail_kept, acc_detail_closed,
              period_closed, year_forward)
            values ('%s', '%s', %d, %d,
              0, 0, 0, 0, 
              0, 0)";
      $rc = $db->execute($sql, $this->newId(), $companyId, $year, $month);
      if ($rc === false) {
        return $this->sqlError(__METHOD__, __LINE__);
      }
    }

    // 操作成功
    $params["year"] = $year;
    $params["name"] = $name;
    return null;
  }
}
