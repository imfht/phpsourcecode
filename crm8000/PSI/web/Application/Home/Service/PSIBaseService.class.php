<?php

namespace Home\Service;

use Home\DAO\UserDAO;

/**
 * Service 基类
 *
 * @author 李静波
 */
class PSIBaseService
{

  /**
   * 是否是演示系统
   */
  protected function isDemo()
  {
    return getenv("IS_DEMO") == "1" || $_SERVER["IS_DEMO"] == "1";
  }

  /**
   * 操作成功
   */
  protected function ok($id = null)
  {
    if ($id) {
      return array(
        "success" => true,
        "id" => $id
      );
    } else {
      return array(
        "success" => true
      );
    }
  }

  /**
   * 操作失败
   *
   * @param string $msg
   *        	错误信息
   */
  protected function bad($msg)
  {
    return array(
      "success" => false,
      "msg" => $msg
    );
  }

  /**
   * 当前功能还没有开发
   *
   * @param string $info
   *        	附加信息
   */
  protected function todo($info = null)
  {
    if ($info) {
      return array(
        "success" => false,
        "msg" => "TODO: 功能还没开发, 附加信息：$info"
      );
    } else {
      return array(
        "success" => false,
        "msg" => "TODO: 功能还没开发"
      );
    }
  }

  /**
   * 数据库错误
   */
  protected function sqlError($codeLine = null)
  {
    $info = "数据库错误，请联系管理员";
    if ($codeLine) {
      $info .= "<br />错误定位：{$codeLine}行";
    }
    return $this->bad($info);
  }

  /**
   * 把时间类型格式化成类似2015-08-13的格式
   */
  protected function toYMD($d)
  {
    return date("Y-m-d", strtotime($d));
  }

  /**
   * 判断当前用户的session是否已经失效
   *
   * @return boolean true: 已经不在线
   */
  protected function isNotOnline()
  {
    $userId = session("loginUserId");
    if ($userId == null) {
      return true;
    } else {
      // 判断当前用户是否被禁用
      // 被禁用的用户，及时当前是在线，也视为已经退出
      $ud = new UserDAO(M());
      if ($ud->isDisabled($userId)) {
        return true;
      }

      return false;
    }
  }

  /**
   * 当用户不在线的时候，返回的提示信息
   *
   * @return array
   */
  protected function notOnlineError()
  {
    return $this->bad("当前用户已经退出系统，请重新登录PSI");
  }

  /**
   * 返回空列表
   *
   * @return array
   */
  protected function emptyResult()
  {
    return array();
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

  protected function tableExists($db, $tableName)
  {
    $dbName = C('DB_NAME');
    $sql = "select count(*) as cnt
				from information_schema.columns
				where table_schema = '%s'
					and table_name = '%s' ";
    $data = $db->query($sql, $dbName, $tableName);
    return $data[0]["cnt"] != 0;
  }

  protected function columnExists($db, $tableName, $columnName)
  {
    $dbName = C('DB_NAME');

    $sql = "select count(*) as cnt
				from information_schema.columns
				where table_schema = '%s'
					and table_name = '%s'
					and column_name = '%s' ";
    $data = $db->query($sql, $dbName, $tableName, $columnName);
    $cnt = $data[0]["cnt"];
    return $cnt == 1;
  }

  /**
   * 当前数据库表结构版本
   */
  protected $CURRENT_DB_VERSION = "20201023-01";
}
