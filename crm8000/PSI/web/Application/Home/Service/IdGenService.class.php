<?php

namespace Home\Service;

use Home\DAO\IdGenDAO;

/**
 * 生成UUIDService
 *
 * @author 李静波
 */
class IdGenService
{

  /**
   * 创建一个新的UUID
   */
  public function newId($db = null)
  {
    if (!$db) {
      $db = M();
    }

    $dao = new IdGenDAO($db);
    return $dao->newId();
  }
}
