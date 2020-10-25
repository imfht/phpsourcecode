<?php

namespace Home\Service;

use Home\DAO\GoodsBomDAO;
use Home\DAO\GoodsBrandDAO;
use Home\DAO\GoodsCategoryDAO;
use Home\DAO\GoodsDAO;
use Home\DAO\GoodsSiDAO;
use Home\DAO\GoodsUnitDAO;
use Home\DAO\PriceSystemDAO;

/**
 * 商品Service
 *
 * @author 李静波
 */
class GoodsService extends PSIBaseExService
{
  private $LOG_CATEGORY_GOODS = "基础数据-物料";
  private $LOG_CATEGORY_UNIT = "基础数据-物料计量单位";
  private $LOG_CATEGORY_BRAND = "基础数据-物料品牌";
  private $LOG_CATEGORY_GOODS_BOM = "基础数据-BOM";
  private $LOG_CATEGORY_PRICE_SYSTEM = "基础数据-价格体系";

  /**
   * 返回所有商品计量单位
   */
  public function allUnits()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new GoodsUnitDAO($this->db());

    return $dao->allUnits();
  }

  /**
   * 所有的启用的商品计量单位
   *
   * @param string $goodsId
   */
  public function allEnabledUnits($goodsId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new GoodsUnitDAO($this->db());
    return $dao->allEnabledUnits($goodsId);
  }

  /**
   * 新建或者编辑 商品计量单位
   */
  public function editUnit($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsUnitDAO($db);

    $log = null;

    if ($id) {
      // 编辑

      $rc = $dao->updateUnit($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑计量单位: $name";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addUnit($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增计量单位: $name";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_UNIT);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除商品计量单位
   */
  public function deleteUnit($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsUnitDAO($db);

    $rc = $dao->deleteUnit($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除计量单位: $name";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_UNIT);

    $db->commit();

    return $this->ok();
  }

  /**
   * 返回所有的商品分类
   */
  public function allCategories($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsCategoryDAO($this->db());
    return $dao->allCategories($params);
  }

  /**
   * 获得某个商品分类的详情
   */
  public function getCategoryInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new GoodsCategoryDAO($this->db());
    return $dao->getCategoryInfo($params);
  }

  /**
   * 新建或者编辑商品分类
   */
  public function editCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsCategoryDAO($db);

    if ($id) {
      // 编辑
      $rc = $dao->updateGoodsCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑物料分类: 编码 = {$code}， 分类名称 = {$name}";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addGoodsCategory($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增物料分类: 编码 = {$code}， 分类名称 = {$name}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除商品分类
   */
  public function deleteCategory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsCategoryDAO($db);

    $rc = $dao->deleteCategory($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $log = "删除物料分类：  编码 = {$code}， 分类名称 = {$name}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS);

    $db->commit();

    return $this->ok();
  }

  /**
   * 商品列表
   */
  public function goodsList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsDAO($this->db());
    return $dao->goodsList($params);
  }

  /**
   * 新建或编辑商品
   */
  public function editGoods($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];

    $db = $this->db();
    $db->startTrans();
    $dao = new GoodsDAO($db);

    $ps = new PinyinService();
    $params["py"] = $ps->toPY($name);
    $params["specPY"] = $ps->toPY($spec);

    $log = null;

    if ($id) {
      // 编辑
      $rc = $dao->updateGoods($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑物料: 物料编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec}";
    } else {
      // 新增

      $params["dataOrg"] = $this->getLoginUserDataOrg();
      $params["companyId"] = $this->getCompanyId();

      $rc = $dao->addGoods($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增物料: 物料编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec}";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 删除商品
   */
  public function deleteGoods($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsDAO($db);

    $rc = $dao->deleteGoods($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $params["code"];
    $name = $params["name"];
    $spec = $params["spec"];
    $log = "删除物料： 物料编码 = {$code}， 品名 = {$name}，规格型号 = {$spec}";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS);

    $db->commit();

    return $this->ok();
  }

  /**
   * 商品字段，查询数据
   */
  public function queryData($queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = [
      "queryKey" => $queryKey,
      "loginUserId" => $this->getLoginUserId(),
      "companyId" => $this->getCompanyId()
    ];

    $dao = new GoodsDAO($this->db());
    return $dao->queryData($params);
  }

  /**
   * 商品字段，查询数据 - 只显示有子商品的商品，用于加工业务中
   */
  public function queryDataForBOM($queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = [
      "queryKey" => $queryKey,
      "loginUserId" => $this->getLoginUserId()
    ];

    $dao = new GoodsDAO($this->db());
    return $dao->queryDataForBOM($params);
  }

  /**
   * 商品字段，查询数据
   */
  public function queryDataWithSalePrice($queryKey, $customerId, $warehouseId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "queryKey" => $queryKey,
      "customerId" => $customerId,
      "warehouseId" => $warehouseId,
      "loginUserId" => $this->getLoginUserId(),
      "companyId" => $this->getCompanyId()
    );

    $dao = new GoodsDAO($this->db());
    return $dao->queryDataWithSalePrice($params);
  }

  /**
   * 商品字段，查询数据
   */
  public function queryDataWithPurchasePrice($queryKey, $supplierId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "queryKey" => $queryKey,
      "supplierId" => $supplierId,
      "loginUserId" => $this->getLoginUserId(),
      "companyId" => $this->getCompanyId()
    );

    $dao = new GoodsDAO($this->db());
    return $dao->queryDataWithPurchasePrice($params);
  }

  /**
   * 获得某个商品的详情
   */
  public function getGoodsInfo($id, $categoryId)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "id" => $id,
      "categoryId" => $categoryId
    );

    $dao = new GoodsDAO($this->db());
    return $dao->getGoodsInfo($params);
  }

  /**
   * 获得某个商品的安全库存列表
   */
  public function goodsSafetyInventoryList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsSiDAO($this->db());
    return $dao->goodsSafetyInventoryList($params);
  }

  /**
   * 获得某个商品安全库存的详情
   */
  public function siInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsSiDAO($this->db());
    return $dao->siInfo($params);
  }

  /**
   * 设置商品的安全
   */
  public function editSafetyInventory($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $json = $params["jsonStr"];
    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $db = $this->db();

    $db->startTrans();

    $dao = new GoodsSiDAO($db);
    $rc = $dao->editSafetyInventory($bill);
    if ($rc) {
      $db->rollback();

      return $rc;
    }

    $goodsCode = $bill["code"];
    $goodsName = $bill["name"];
    $goodsSpec = $bill["spec"];
    $bs = new BizlogService($db);
    $log = "为物料[$goodsCode $goodsName $goodsSpec]设置安全库存";
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS);

    $db->commit();

    return $this->ok();
  }

  /**
   * 通过条形码查询商品信息, 销售出库单使用
   */
  public function queryGoodsInfoByBarcode($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new GoodsDAO($this->db());
    return $dao->queryGoodsInfoByBarcode($params);
  }

  /**
   * 通过条形码查询商品信息, 采购入库单使用
   */
  public function queryGoodsInfoByBarcodeForPW($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new GoodsDAO($this->db());
    return $dao->queryGoodsInfoByBarcodeForPW($params);
  }

  /**
   * 查询商品种类总数
   */
  public function getTotalGoodsCount($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsDAO($this->db());
    return $dao->getTotalGoodsCount($params);
  }

  /**
   * 获得所有的品牌
   */
  public function allBrands()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = array(
      "loginUserId" => $this->getLoginUserId()
    );

    $dao = new GoodsBrandDAO($this->db());
    return $dao->allBrands($params);
  }

  /**
   * 新增或编辑商品品牌
   */
  public function editBrand($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $id = $params["id"];
    $name = $params["name"];

    $ps = new PinyinService();
    $params["py"] = $ps->toPY($name);

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsBrandDAO($db);

    $log = null;

    $params["dataOrg"] = $this->getLoginUserDataOrg();
    $params["companyId"] = $this->getCompanyId();

    if ($id) {
      // 编辑品牌

      $rc = $dao->updateGoodsBrand($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $log = "编辑物料品牌[$name]";
    } else {
      // 新增品牌

      $rc = $dao->addBrand($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];

      $log = "新增物料品牌[$name]";
    }

    // 记录业务日志
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_BRAND);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 获得某个品牌的上级品牌全称
   */
  public function brandParentName($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new GoodsBrandDAO($this->db());
    return $dao->brandParentName($params);
  }

  /**
   * 删除商品品牌
   */
  public function deleteBrand($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsBrandDAO($db);

    $rc = $dao->deleteBrand($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $fullName = $params["fullName"];
    $log = "删除物料品牌[$fullName]";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_BRAND);

    $db->commit();

    return $this->ok();
  }

  /**
   * 商品构成
   */
  public function goodsBOMList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["companyId"] = $this->getCompanyId();

    $dao = new GoodsBomDAO($this->db());
    return $dao->goodsBOMList($params);
  }

  /**
   * 新增或编辑商品构成
   *
   * @param array $params
   */
  public function editGoodsBOM($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $params["companyId"] = $this->getCompanyId();

    $dao = new GoodsBomDAO($db);

    $addBOM = $params["addBOM"] == "1";
    $rc = null;
    if ($addBOM) {
      $rc = $dao->addGoodsBOM($params);
    } else {
      $rc = $dao->updateGoodsBOM($params);
    }

    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $goodsInfo = "编码：" . $params["goodsCode"] . " 名称：" . $params["goodsName"] . " 规格: " . $params["goodsSpec"];
    $subGoodsInfo = "编码： " . $params["subGoodsCode"] . " 名称：" . $params["subGoodsName"] . " 规格：" . $params["subGoodsSpec"];

    $log = null;
    if ($addBOM) {
      $log = "给物料[$goodsInfo]新增子物料[$subGoodsInfo]";
    } else {
      $log = "编辑物料[$goodsInfo]的子物料[$subGoodsInfo]信息 ";
    }
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS_BOM);

    $db->commit();

    return $this->ok();
  }

  /**
   * 子商品字段，查询数据
   */
  public function queryDataForSubGoods($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsDAO($this->db());
    return $dao->queryDataForSubGoods($params);
  }

  /**
   * 查询子商品的详细信息
   */
  public function getSubGoodsInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new GoodsBomDAO($this->db());
    return $dao->getSubGoodsInfo($params);
  }

  /**
   * 删除商品构成中的子商品
   *
   * @param array $params
   */
  public function deleteGoodsBOM($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new GoodsBomDAO($db);

    $rc = $dao->deleteGoodsBOM($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $bs = new BizlogService($db);
    $goodsInfo = "编码：" . $params["goodsCode"] . " 名称：" . $params["goodsName"] . " 规格: " . $params["goodsSpec"];
    $subGoodsInfo = "编码： " . $params["subGoodsCode"] . " 名称：" . $params["subGoodsName"] . " 规格：" . $params["subGoodsSpec"];
    $log = "从物料[$goodsInfo]中删除子物料[$subGoodsInfo]";
    $bs->insertBizlog($log, $this->LOG_CATEGORY_GOODS_BOM);

    $db->commit();

    return $this->ok();
  }

  /**
   * 新增或编辑价格体系中的价格
   */
  public function editPriceSystem($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $params["companyId"] = $this->getCompanyId();
    $params["dataOrg"] = $this->getLoginUserDataOrg();

    $db = $this->db();

    $db->startTrans();

    $id = $params["id"];
    $dao = new PriceSystemDAO($db);
    $log = null;
    if ($id) {
      // 编辑
      $rc = $dao->updatePriceSystem($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $name = $params["name"];
      $log = "编辑价格体系[$name]";
    } else {
      // 新增
      $rc = $dao->addPriceSystem($params);
      if ($rc) {
        $db->rollback();
        return $rc;
      }

      $id = $params["id"];
      $name = $params["name"];
      $log = "新增价格体系[$name]";
    }

    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_PRICE_SYSTEM);

    $db->commit();

    return $this->ok($id);
  }

  /**
   * 价格体系-价格列表
   */
  public function priceSystemList()
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = [
      "loginUserId" => $this->getLoginUserId()
    ];
    $dao = new PriceSystemDAO($this->db());
    return $dao->priceSystemList($params);
  }

  /**
   * 删除价格体系中的价格
   */
  public function deletePriceSystem($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $db = $this->db();
    $db->startTrans();

    $dao = new PriceSystemDAO($db);

    $rc = $dao->deletePriceSystem($params);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $name = $params["name"];
    $log = "删除价格体系中的价格[$name]";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_PRICE_SYSTEM);

    $db->commit();

    return $this->ok();
  }

  /**
   * 查询某个商品的所有价格体系里面的价格列表
   */
  public function goodsPriceSystemList($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PriceSystemDAO($this->db());
    return $dao->goodsPriceSystemList($params);
  }

  /**
   * 查询某个商品的价格体系中所有价格的值
   */
  public function goodsPriceSystemInfo($params)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $dao = new PriceSystemDAO($this->db());
    return $dao->goodsPriceSystemInfo($params);
  }

  /**
   * 设置商品价格体系中的价格
   */
  public function editGoodsPriceSystem($params)
  {
    if ($this->isNotOnline()) {
      return $this->notOnlineError();
    }

    $json = $params["jsonStr"];
    $bill = json_decode(html_entity_decode($json), true);
    if ($bill == null) {
      return $this->bad("传入的参数错误，不是正确的JSON格式");
    }

    $db = $this->db();
    $db->startTrans();

    $bill["dataOrg"] = $this->getLoginUserDataOrg();
    $bill["companyId"] = $this->getCompanyId();

    $dao = new PriceSystemDAO($db);

    $rc = $dao->editGoodsPriceSystem($bill);
    if ($rc) {
      $db->rollback();
      return $rc;
    }

    $code = $bill["code"];
    $name = $bill["name"];
    $spec = $bill["spec"];
    $log = "设置物料[$code $name $spec]的价格体系";
    $bs = new BizlogService($db);
    $bs->insertBizlog($log, $this->LOG_CATEGORY_PRICE_SYSTEM);

    $db->commit();

    return $this->ok();
  }

  /**
   * 商品品牌自定义字段，查询数据
   */
  public function queryGoodsBrandData($queryKey)
  {
    if ($this->isNotOnline()) {
      return $this->emptyResult();
    }

    $params = [
      "queryKey" => $queryKey,
      "loginUserId" => $this->getLoginUserId()
    ];

    $dao = new GoodsBrandDAO($this->db());
    return $dao->queryGoodsBrandData($params);
  }

  /**
   * 导出Excel
   */
  public function exportExcel()
  {
    if ($this->isNotOnline()) {
      return;
    }

    $params["loginUserId"] = $this->getLoginUserId();

    $dao = new GoodsDAO($this->db());

    $data = $dao->getDataForExcel($params);

    // 记录业务日志
    $log = "物料导出Excel文件";
    $bls = new BizlogService($this->db());
    $bls->insertBizlog($log, $this->LOG_CATEGORY_GOODS);

    $excel = new \PHPExcel();

    $sheet = $excel->getActiveSheet();
    if (!$sheet) {
      $sheet = $excel->createSheet();
    }

    $sheet->setTitle("物料");

    $sheet->getRowDimension('1')->setRowHeight(22);
    $sheet->setCellValue("A1", "");

    $sheet->getColumnDimension('A')->setWidth(40);
    $sheet->setCellValue("A2", "物料分类");

    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->setCellValue("B2", "物料编码");

    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->setCellValue("C2", "品名");

    $sheet->getColumnDimension('D')->setWidth(80);
    $sheet->setCellValue("D2", "规格型号");

    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->setCellValue("E2", "计量单位");

    $sheet->getColumnDimension('F')->setWidth(40);
    $sheet->setCellValue("F2", "品牌");

    $sheet->getColumnDimension('G')->setWidth(15);
    $sheet->setCellValue("G2", "销售基准价");

    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->setCellValue("H2", "建议采购价");

    $sheet->getColumnDimension('I')->setWidth(15);
    $sheet->setCellValue("I2", "税率");

    $sheet->getColumnDimension('J')->setWidth(15);
    $sheet->setCellValue("J2", "条形码");

    $sheet->getColumnDimension('K')->setWidth(40);
    $sheet->setCellValue("K2", "备注");

    $sheet->getColumnDimension('L')->setWidth(15);
    $sheet->setCellValue("L2", "状态");

    foreach ($data as $i => $v) {
      $row = $i + 3;
      $sheet->setCellValue("A" . $row, $v["categoryName"]);
      $sheet->setCellValue("B" . $row, $v["code"]);
      $sheet->setCellValue("C" . $row, $v["name"]);
      $sheet->setCellValue("D" . $row, $v["spec"]);
      $sheet->setCellValue("E" . $row, $v["unitName"]);
      $sheet->setCellValue("F" . $row, $v["brandFullName"]);
      $sheet->setCellValue("G" . $row, $v["salePrice"]);
      $sheet->setCellValue("H" . $row, $v["purchasePrice"]);
      $sheet->setCellValue("I" . $row, $v["taxRate"]);
      $sheet->setCellValue("J" . $row, $v["barCode"]);
      $sheet->setCellValue("K" . $row, $v["memo"]);
      $sheet->setCellValue("L" . $row, $v["recordStatus"]);
    }

    // 画表格边框
    $styleArray = [
      'borders' => [
        'allborders' => [
          'style' => 'thin'
        ]
      ]
    ];
    $lastRow = count($data) + 2;
    $sheet->getStyle('A2:L' . $lastRow)->applyFromArray($styleArray);

    $dt = date("YmdHis");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="物料_' . $dt . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = \PHPExcel_IOFactory::createWriter($excel, "Excel2007");
    $writer->save("php://output");
  }
}
