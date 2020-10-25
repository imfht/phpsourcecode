<?php

namespace Home\Service;

use Home\DAO\MainMenuDAO;

/**
 * 主菜单Service
 *
 * @author 李静波
 */
class MainMenuService extends PSIBaseExService
{
  private $LOG_CATEGORY = "主菜单维护";

  /**
   * 当前用户有权限访问的所有菜单项
   */
  public function mainMenuItems()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $us = new UserService();

    $sql = "select id, caption, fid from (select * from t_menu_item union select * from t_menu_item_plus) m
					where parent_id is null order by show_order";
    $db = M();
    $m1 = $db->query($sql);
    $result = array();

    $index1 = 0;
    foreach ($m1 as $menuItem1) {

      $children1 = array();

      $sql = "select id, caption, fid from (select * from t_menu_item union select * from t_menu_item_plus) m
						where parent_id = '%s' order by show_order ";
      $m2 = $db->query($sql, $menuItem1["id"]);

      // 第二级菜单
      $index2 = 0;
      foreach ($m2 as $menuItem2) {
        $children2 = array();
        $sql = "select id, caption, fid from (select * from t_menu_item union select * from t_menu_item_plus) m
							where parent_id = '%s' order by show_order ";
        $m3 = $db->query($sql, $menuItem2["id"]);

        // 第三级菜单
        $index3 = 0;
        foreach ($m3 as $menuItem3) {
          if ($us->hasPermission($menuItem3["fid"])) {
            $children2[$index3]["id"] = $menuItem3["id"];
            $children2[$index3]["caption"] = $menuItem3["caption"];
            $children2[$index3]["fid"] = $menuItem3["fid"];
            $children2[$index3]["children"] = array();
            $index3++;
          }
        }

        $fid = $menuItem2["fid"];
        if ($us->hasPermission($fid)) {
          if ($fid) {
            // 仅有二级菜单
            $children1[$index2]["id"] = $menuItem2["id"];
            $children1[$index2]["caption"] = $menuItem2["caption"];
            $children1[$index2]["fid"] = $menuItem2["fid"];
            $children1[$index2]["children"] = $children2;
            $index2++;
          } else {
            if (count($children2) > 0) {
              // 二级菜单还有三级菜单
              $children1[$index2]["id"] = $menuItem2["id"];
              $children1[$index2]["caption"] = $menuItem2["caption"];
              $children1[$index2]["fid"] = $menuItem2["fid"];
              $children1[$index2]["children"] = $children2;
              $index2++;
            }
          }
        }
      }

      if (count($children1) > 0) {
        $result[$index1] = $menuItem1;
        $result[$index1]["children"] = $children1;
        $index1++;
      }
    }

    return $result;
  }

  /**
   * 查询所有的主菜单项 - 主菜单维护模块中使用
   */
  public function allMenuItemsForMaintain()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new MainMenuDAO($this->db());
    return $dao->allMenuItemsForMaintain();
  }

  /**
   * Fid字段查询数据
   */
  public function queryDataForFid($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new MainMenuDAO($this->db());
    return $dao->queryDataForFid($params);
  }

  /**
   * 菜单项自定义字段 - 查询数据
   */
  public function queryDataForMenuItem($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new MainMenuDAO($this->db());
    return $dao->queryDataForMenuItem($params);
  }

  /**
   * 菜单项快捷访问自定义字段 - 查询数据
   */
  public function queryDataForShortcut($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new MainMenuDAO($this->db());
    return $dao->queryDataForShortcut($params);
  }

  /**
   * 主菜单维护 - 新增或编辑菜单项
   */
  public function editMenuItem($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["isDemo"] = $this->isDemo();

    $pyService = new PinyinService();


    $db = $this->db();
    $db->startTrans();

    $id = $params["id"];
    $caption = $params["caption"];
    $py = $pyService->toPY($caption);
    $params["py"] = $py;

    $log = null;
    $dao = new MainMenuDAO($db);
    if ($id) {
      // 编辑
      $rc = $dao->updateMenuItem($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑主菜单项：{$caption}";
    } else {
      // 新增
      $rc = $dao->addMenuItem($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $log = "新增主菜单项：{$caption}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除菜单项
   */
  public function deleteMenuItem($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new MainMenuDAO($db);
    $rc = $dao->deleteMenuItem($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $caption = $params["caption"];
    $log = "删除主菜单项：{$caption}";

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY);

    $db->commit();

    return $this->ok();
  }

  /**
   * 某个菜单项的详情信息
   */
  public function menuItemInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new MainMenuDAO($this->db());
    return $dao->menuItemInfo($params);
  }
}
