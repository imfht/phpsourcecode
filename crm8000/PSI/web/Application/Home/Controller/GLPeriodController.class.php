<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\UserService;
use Home\Service\GLPeriodService;

/**
 * 会计期间Controller
 *
 * @author 李静波
 *        
 */
class GLPeriodController extends PSIBaseController
{

  /**
   * 会计期间 - 主页面
   */
  public function index()
  {
    $us = new UserService();

    if ($us->hasPermission(FIdConst::GL_PERIOD)) {
      $this->initVar();

      $this->assign("title", "会计期间");

      $this->display();
    } else {
      $this->gotoLoginPage("/Home/GLPeriod/index");
    }
  }

  /**
   * 返回所有的公司列表
   */
  public function companyList()
  {
    if (IS_POST) {
      $service = new GLPeriodService();
      $this->ajaxReturn($service->companyList());
    }
  }

  /**
   * 某个公司的全部会计期间
   */
  public function periodList()
  {
    if (IS_POST) {
      $params = [
        "companyId" => I("post.companyId")
      ];

      $service = new GLPeriodService();
      $this->ajaxReturn($service->periodList($params));
    }
  }

  /**
   * 初始化某个公司的本年度会计期间
   */
  public function initPeriod()
  {
    if (IS_POST) {
      $params = [
        "companyId" => I("post.companyId")
      ];

      $service = new GLPeriodService();
      $this->ajaxReturn($service->initPeriod($params));
    }
  }
}
