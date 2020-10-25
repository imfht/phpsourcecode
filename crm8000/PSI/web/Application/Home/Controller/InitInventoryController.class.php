<?php

namespace Home\Controller;

use Home\Service\InitInventoryService;

/**
 * 库存建账Controller
 *
 * @author 李静波
 *        
 */
class InitInventoryController extends PSIBaseController
{

  /**
   * 查询仓库列表
   */
  public function warehouseList()
  {
    if (IS_POST) {
      $is = new InitInventoryService();
      $this->ajaxReturn($is->warehouseList());
    }
  }

  /**
   * 获得建账信息列表
   */
  public function initInfoList()
  {
    if (IS_POST) {
      $params = array(
        "warehouseId" => I("post.warehouseId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $is = new InitInventoryService();
      $this->ajaxReturn($is->initInfoList($params));
    }
  }

  /**
   * 录入建账信息时候，获得商品分类列表
   */
  public function goodsCategoryList()
  {
    if (IS_POST) {
      $is = new InitInventoryService();
      $this->ajaxReturn($is->goodsCategoryList());
    }
  }

  /**
   * 录入建账信息的时候，获得商品列表
   */
  public function goodsList()
  {
    if (IS_POST) {
      $params = array(
        "warehouseId" => I("post.warehouseId"),
        "categoryId" => I("post.categoryId"),
        "page" => I("post.page"),
        "start" => I("post.start"),
        "limit" => I("post.limit")
      );
      $is = new InitInventoryService();
      $this->ajaxReturn($is->goodsList($params));
    }
  }

  /**
   * 提交建账信息
   */
  public function commitInitInventoryGoods()
  {
    if (IS_POST) {
      $params = array(
        "warehouseId" => I("post.warehouseId"),
        "goodsId" => I("post.goodsId"),
        "goodsCount" => I("post.goodsCount"),
        "goodsMoney" => I("post.goodsMoney")
      );
      $is = new InitInventoryService();
      $this->ajaxReturn($is->commitInitInventoryGoods($params));
    }
  }

  /**
   * 标记完成建账
   */
  public function finish()
  {
    if (IS_POST) {
      $params = array(
        "warehouseId" => I("post.warehouseId")
      );
      $is = new InitInventoryService();
      $this->ajaxReturn($is->finish($params));
    }
  }

  /**
   * 取消建账完成标记
   */
  public function cancel()
  {
    if (IS_POST) {
      $params = array(
        "warehouseId" => I("post.warehouseId")
      );
      $is = new InitInventoryService();
      $this->ajaxReturn($is->cancel($params));
    }
  }
}
