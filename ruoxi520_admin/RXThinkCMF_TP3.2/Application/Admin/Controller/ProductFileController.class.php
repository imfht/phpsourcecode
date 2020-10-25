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
 * 商品附件-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-21
 */
namespace Admin\Controller;
use Admin\Model\ProductFileModel;
use Admin\Service\ProductFileService;
class ProductFileController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new ProductFileModel();
        $this->service = new ProductFileService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-12-21
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        parent::index([
            'product_id'=>(int)$_GET['product_id'],
        ]);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-12-21
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::edit()
     */
    function edit() {
        parent::edit([
            'product_id'=>$_GET['product_id'],
        ]);
    }

}