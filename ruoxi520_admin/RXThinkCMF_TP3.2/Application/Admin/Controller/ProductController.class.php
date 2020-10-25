<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 商品-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Controller;
use Admin\Model\ProductModel;
use Admin\Service\ProductService;
use Admin\Service\CateAttributeService;
use Admin\Model\ProductImageModel;
use Admin\Service\CateService;
class ProductController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new ProductModel();
        $this->service = new ProductService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-12-18(non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        if($_GET['simple']) {
            $this->render("product.simple.html");
            return;
        }
        parent::index();
    }
    
    /**
     * 设置规格状态
     *
     * @author 牧羊人
     * @date 2018-10-25
     */
    function setIsSpec() {
        if(IS_POST) {
            $message = $this->service->setIsSpec();
            $this->ajaxReturn($message);
            return ;
        }
    }
    
    /**
     * 商品规格
     * 
     * @author 牧羊人
     * @date 2018-10-24
     */
    function productModel() {
        if(IS_POST) {
            $message = $this->service->productModel();
            $this->ajaxReturn($message);
            return ;
        }
        $productId = I("get.product_id",0);
        $this->assign('product_id',$productId);
        
        //获取商品属性
        $categoryAttrService = new CateAttributeService();
        $list = $categoryAttrService->getAttributeList();
        $this->assign('list',json_encode($list));
        
        //获取SKU列表
        $skuList = $this->service->getSkuList($productId);
        $this->assign('skuList',json_encode($skuList));

        $this->render();
    }
    
    /**
     * 上传SKU图集
     * 
     * @author 牧羊人
     * @date 2018-11-01
     */
    function skuImgs() {
        if(IS_POST) {
            $message = $this->service->skuImgs();
            $this->ajaxReturn($message);
            return;
        }
        //SKU编号
        $skuId = (int)$_GET['sku_id'];
     
        $productImageMod = new ProductImageModel();
        $result = $productImageMod->getRowByAttr([
            'sku_id'=>$skuId,
        ],'id');
        if($result) {
            $info = $productImageMod->getInfo((int)$result['id']);
        }else{
            $info['sku_id'] = $skuId;
        }
        $this->assign('info',$info);
        $this->render();
    }
    
    /**
     * 阶梯报价
     * 
     * @author 牧羊人
     * @date 2018-12-24
     */
    function ladderPrice() {
        if(IS_POST) {
            $message = $this->service->ladderPrice();
            $this->ajaxReturn($message);
            return ;
        }
        $productId = (int)$_GET['product_id'];
        if($productId) {
            $info = $this->mod->getInfo($productId);
        }else{
            $info['id'] = $productId;
        }
        $this->assign('info',$info);
        $this->render();
    }
    
    /**
     * 分类选择【商品选择分类】
     *
     * @author 牧羊人
     * @date 2018-12-19
     */
    function cateSelect() {
        $cateService = new CateService();
        $list = $cateService->cateSelect();
        $this->assign('list',$list);
        $this->render();
    }
    
    /**
     * 选择属性
     * 
     * @author 牧羊人
     * @date 2018-12-26
     */
    function attrSelect() {
        $list = $this->service->attrSelect();
        $this->assign('list', $list);
        $this->render();
    }
    
}