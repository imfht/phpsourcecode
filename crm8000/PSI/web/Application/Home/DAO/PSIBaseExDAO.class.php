<?php

namespace Home\DAO;

/**
 * 基础 DAO
 *
 * @author 李静波
 */
class PSIBaseExDAO extends PSIBaseDAO
{
  /**
   *
   * @var \Think\Model $db
   */
  protected $db;

  function __construct($db)
  {
    $this->db = $db;
  }

  /**
   * 生成全局唯一Id （UUID）
   *
   * @return string
   */
  public function newId()
  {
    $db = $this->db;

    $data = $db->query("select UUID() as uuid");

    return strtoupper($data[0]["uuid"]);
  }

  protected function loginUserIdNotExists($loginUserId)
  {
    $db = $this->db;

    $sql = "select count(*) as cnt from t_user where id = '%s' ";
    $data = $db->query($sql, $loginUserId);
    $cnt = $data[0]["cnt"];

    return $cnt != 1;
  }

  protected function dataOrgNotExists($dataOrg)
  {
    $db = $this->db;

    $sql = "select count(*) as cnt from t_user where data_org = '%s' ";
    $data = $db->query($sql, $dataOrg);
    $cnt = $data[0]["cnt"];

    return $cnt != 1;
  }

  protected function companyIdNotExists($companyId)
  {
    $db = $this->db;

    $sql = "select count(*) as cnt from t_org where id = '%s' ";
    $data = $db->query($sql, $companyId);
    $cnt = $data[0]["cnt"];

    return $cnt != 1;
  }

  /**
   * 判断日期是否是正确的Y-m-d格式
   *
   * @param string $date        	
   * @return boolean true: 是正确的格式
   */
  protected function dateIsValid($date)
  {
    $dt = strtotime($date);
    if (!$dt) {
      return false;
    }

    return date("Y-m-d", $dt) == $date;
  }

  /**
   * 空结果
   *
   * @return array
   */
  protected function emptyResult()
  {
    return [];
  }

  /**
   * 参数错误
   *
   * @param string $param
   *        	参数名称
   * @return array
   */
  protected function badParam($param)
  {
    return $this->bad("参数" . $param . "不正确");
  }

  /**
   * 把输入字符串前后的空格去掉后，判断是否是空字符串
   *
   * @param string $s        	
   *
   * @return true: 空字符串
   */
  protected function isEmptyStringAfterTrim($s)
  {
    $result = trim($s);
    return $result == null || $result == "";
  }

  /**
   * 判断字符串长度是否超过限度
   *
   * @param string $s        	
   * @param int $length
   *        	默认长度不能超过255
   * @return bool true：超过了限度
   */
  protected function stringBeyondLimit(string $s, int $length = 255): bool
  {
    return strlen($s) > $length;
  }
}
