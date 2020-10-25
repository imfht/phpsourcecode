<?php

namespace Home\Service;

/**
 * FId Service
 *
 * @author 李静波
 */
class FIdService
{

  /**
   * 记录刚刚操作过的FId值
   */
  public function insertRecentFid($fid)
  {
    if ($fid == null) {
      return;
    }

    $us = new UserService();
    $userId = $us->getLoginUserId();
    if (!$userId) {
      return;
    }

    $db = M();

    $sql = "select click_count from t_recent_fid where user_id = '%s' and fid = '%s' ";
    $data = $db->query($sql, $userId, $fid);

    if ($data) {
      $clickCount = $data[0]["click_count"];
      $clickCount++;

      $sql = "update t_recent_fid 
					set click_count = %d 
					where user_id = '%s' and fid = '%s' ";
      $db->execute($sql, $clickCount, $userId, $fid);
    } else {
      $sql = "insert into t_recent_fid(fid, user_id, click_count) values ('%s', '%s',  1)";

      $db->execute($sql, $fid, $userId);
    }
  }

  public function recentFid()
  {
    $us = new UserService();
    $userId = $us->getLoginUserId();

    //
    // 这里的SQL里面之所以和 t_permission、t_role_permission有关联
    // 是为了处理：某个模块权限原来有，但是现在没有了，这样在常用功能里面就不应该出现该模块
    //
    // SQL的select部分有一个不需要返回给前端的 r.click_count，是因为在MySQL 5.7+因为SQL_MODE的原因
    // 不加上r.click_count就会出错。
    //
    $sql = " select distinct f.fid, f.name, r.click_count 
				from t_recent_fid r,  
					(select * from t_fid union select * from t_fid_plus) f, 
					(select * from t_permission union select * from t_permission_plus) p, 
					t_role_permission rp, t_role_user ru
				where r.fid = f.fid and r.user_id = '%s' and r.fid = p.fid 
				and p.id = rp.permission_id and rp.role_id = ru.role_id 
				and ru.user_id = '%s' 
				order by r.click_count desc
				limit 10";

    $data = M()->query($sql, $userId, $userId);

    $result = [];
    foreach ($data as $v) {
      $result[] = [
        "fid" => $v["fid"],
        "name" => $v["name"]
      ];
    }

    return $result;
  }

  public function getFIdName($fid)
  {
    $sql = "select name from (select * from t_fid union select * from t_fid_plus) f where fid = '%s' ";
    $data = M()->query($sql, $fid);
    if ($data) {
      return $data[0]["name"];
    } else {
      return null;
    }
  }
}
