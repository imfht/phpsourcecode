<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\BizlogService;
use Home\Service\UpdateDBService;
use Home\Service\UserService;

/**
 * 业务日志Controller
 *
 * @author 李静波
 *        
 */
class BizlogController extends PSIBaseController
{

  /**
   * 业务日志 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::BIZ_LOG)) {
      $this->initVar();

      $this->assign("title", "业务日志");

      $this->assign("canUnitTest", $this->canUnitTest() ? 1 : 0);

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/Bizlog/index");
    }
  }

  /**
   * 查询业务日志
   */
  public function logList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::BIZ_LOG)) {
        die("没有权限");
      }

      $params = [
        "loginName" => I("post.loginName"),
        "userId" => I("post.userId"),
        "ip" => I("post.ip"),
        "fromDT" => I("post.fromDT"),
        "toDT" => I("post.toDT"),
        "logCategory" => I("post.logCategory"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      ];

      $bs = new BizlogService();
      $this->ajaxReturn($bs->logList($params));
    }
  }

  /**
   * 返回所有的日志分类
   */
  public function getLogCategoryList()
  {
    if (IS_POST) {
      $us = new UserService();
      if (!$us->hasPermission(FIdConst::BIZ_LOG)) {
        die("没有权限");
      }

      $params = [];
      $service = new BizlogService();
      $this->ajaxReturn($service->getLogCategoryList($params));
    }
  }

  /**
   * 升级数据库
   */
  public function updateDatabase()
  {
    if (IS_POST) {
      $bs = new UpdateDBService();
      $this->ajaxReturn($bs->updateDatabase());
    }
  }
}
