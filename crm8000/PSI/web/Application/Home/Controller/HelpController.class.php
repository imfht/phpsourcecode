<?php

namespace Home\Controller;

use Home\Service\BizlogService;

/**
 * 帮助Controller
 *
 * @author 李静波
 *        
 */
class HelpController extends PSIBaseController
{

  public function index()
  {
    $bs = new BizlogService();

    $key = I("get.t");
    switch ($key) {
      case "login":
        // 用户直接访问登录帮助的时候，多半还没有登录所以没法记录业务日志
        redirect("/help/10.html");
        break;
      case "user":
        $bs->insertBizlog("访问帮助页面：用户管理", "帮助");
        redirect("/help/02-01.html");
        break;
      case "priceSystem":
        $bs->insertBizlog("访问帮助页面：价格体系", "帮助");
        redirect("/help/02-04-03.html");
        break;
      case "initInv":
        $bs->insertBizlog("访问帮助页面：库存建账", "帮助");
        redirect("/help/02-06.html");
        break;
      case "permission":
        $bs->insertBizlog("访问帮助页面：权限管理", "帮助");
        redirect("/help/02-02.html");
        break;
      case "bizlog":
        $bs->insertBizlog("访问帮助页面：业务日志", "帮助");
        redirect("/help/03.html");
        break;
      case "warehouse":
        $bs->insertBizlog("访问帮助页面：仓库", "帮助");
        redirect("/help/02-05.html");
        break;
      case "goods":
        $bs->insertBizlog("访问帮助页面：物料", "帮助");
        redirect("/help/02-04.html");
        break;
      case "goodsBrand":
        $bs->insertBizlog("访问帮助页面：物料品牌", "帮助");
        redirect("/help/02-04-02.html");
        break;
      case "goodsUnit":
        $bs->insertBizlog("访问帮助页面：物料计量单位", "帮助");
        redirect("/help/02-04-01.html");
        break;
      case "supplier":
        $bs->insertBizlog("访问帮助页面：供应商档案", "帮助");
        redirect("/help/02-07.html");
        break;
      case "customer":
        $bs->insertBizlog("访问帮助页面：客户资料", "帮助");
        redirect("/help/02-08.html");
        break;
      case "bizconfig":
        $bs->insertBizlog("访问帮助页面：业务设置", "帮助");
        redirect("/help/02-03.html");
        break;
      case "pobill":
        $bs->insertBizlog("访问帮助页面：采购订单", "帮助");
        redirect("/help/20-01.html");
        break;
      case "pwbill":
        $bs->insertBizlog("访问帮助页面：采购入库", "帮助");
        redirect("/help/20-02.html");
        break;
      case "prbill":
        $bs->insertBizlog("访问帮助页面：采购退货出库", "帮助");
        redirect("/help/20-03.html");
        break;
      case "sobill":
        $bs->insertBizlog("访问帮助页面：销售订单", "帮助");
        redirect("/help/30-01.html");
        break;
      case "wsbill":
        $bs->insertBizlog("访问帮助页面：销售出库", "帮助");
        redirect("/help/30-02.html");
        break;
      case "srbill":
        $bs->insertBizlog("访问帮助页面：销售退货入库", "帮助");
        redirect("/help/30-03.html");
        break;
      case "itbill":
        $bs->insertBizlog("访问帮助页面：库间调拨", "帮助");
        redirect("/help/40-01.html");
        break;
      case "icbill":
        $bs->insertBizlog("访问帮助页面：库存盘点", "帮助");
        redirect("/help/40-02.html");
        break;
      case "dataOrg":
        $bs->insertBizlog("访问帮助页面：数据域应用详解", "帮助");
        redirect("/help/05.html");
        break;
      case "commBill":
        $bs->insertBizlog("访问帮助页面：表单通用操作", "帮助");
        redirect("/help/00.html");
        break;
      case "scbill":
        $bs->insertBizlog("访问帮助页面：销售合同", "帮助");
        redirect("/help/30-04.html");
        break;
      case "costWeight":
        $bs->insertBizlog("访问帮助页面：BOM-成本分摊权重", "帮助");
        redirect("/help/02-04-04.html");
        break;
      case "wspbill":
        $bs->insertBizlog("访问帮助页面：存货拆分", "帮助");
        redirect("/help/60-01.html");
        break;
      case "factory":
        $bs->insertBizlog("访问帮助页面：工厂", "帮助");
        redirect("/help/02-09.html");
        break;
      case "dmobill":
        $bs->insertBizlog("访问帮助页面：成品委托生产订单", "帮助");
        redirect("/help/60-02.html");
        break;
      case "dmwbill":
        $bs->insertBizlog("访问帮助页面：成品委托生产入库", "帮助");
        redirect("/help/60-03.html");
        break;
      case "mainMenuMaintain":
        $bs->insertBizlog("访问帮助页面：主菜单维护", "帮助");
        redirect("/help/08-01.html");
        break;
      case "sysdict":
        $bs->insertBizlog("访问帮助页面：系统数据字典", "帮助");
        redirect("/help/08-02.html");
        break;
      case "codetable":
        $bs->insertBizlog("访问帮助页面：码表设置", "帮助");
        redirect("/help/08-03.html");
        break;
      case "formview":
        $bs->insertBizlog("访问帮助页面：视图开发助手", "帮助");
        redirect("/help/08-04.html");
        break;
      default:
        $bs->insertBizlog("通过主菜单进入帮助页面", "帮助");
        redirect("/help/index.html");
    }
  }
}
