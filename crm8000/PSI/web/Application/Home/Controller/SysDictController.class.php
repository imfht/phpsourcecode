<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\UserService;
use Home\Service\SysDictService;

/**
 * 系统数据字典Controller
 *
 * @author 李静波
 *        
 */
class SysDictController extends PSIBaseController
{

  /**
   * 系统数据字典 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::SYS_DICT)) {
      $this->initVar();

      $this->assign("title", "系统数据字典");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/SysDict/index");
    }
  }

  /**
   * 数据字典分类列表
   */
  public function categoryList()
  {
    if (IS_POST) {
      $params = [];

      $service = new SysDictService();
      $this->ajaxReturn($service->categoryList($params));
    }
  }

  /**
   * 某个分类下的数据字典
   */
  public function sysDictList()
  {
    if (IS_POST) {
      $params = [
        "categoryId" => I("post.categoryId")
      ];

      $service = new SysDictService();
      $this->ajaxReturn($service->sysDictList($params));
    }
  }

  /**
   * 查询某个码表的数据
   */
  public function dictDataList()
  {
    if (IS_POST) {
      $params = [
        "id" => I("post.id")
      ];

      $service = new SysDictService();
      $this->ajaxReturn($service->dictDataList($params));
    }
  }
}
