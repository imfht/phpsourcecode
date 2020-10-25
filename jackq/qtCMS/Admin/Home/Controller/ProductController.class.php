<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:33
 */

namespace Home\Controller;


class ProductController extends CommonController
{

    public function index(){
        $result = $this->getPagination('Product');
        //var_dump($result['data']);
        $this->assign('produts', $result['data']);
        $this->assign('rows_count', $result['total_rows']);
        $this->assign('page', $result['show']);
        $this->display();
    }

    public function add(){
        $categorys =  D('Category', 'Service')->getCategorysByRelatinModel('Product');
        $this->assign('categorys',$categorys);
        $this->display();
    }

    /**
     * 创建
     * @return
     */
    public function create() {
        if (!isset($_POST['product'])) {
            return $this->errorReturn('无效的操作！');
        }
        if(empty($_POST['imageType'])){
             return $this->errorReturn('商品没有图片，或至少一张图片设置为主图！');
        }
        $result = D('Product', 'Service')->add($_POST['product'],$_POST['plu_imageIds']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->successReturn('添加产品信息成功！', U('Product/index'));
    }

    /**
     * 编辑
     * @return
     */
    public function edit() {
        $productService = D('Product', 'Service');
        if (!isset($_GET['id']) || !$productService->existById($_GET['id'])) {
            return $this->error('需要编辑的产品不存在！');
        }
        $product = $productService->getById($_GET['id']);
        $this->assign('product', $product);
        $categorys =  D('Category', 'Service')->getCategorysByRelatinModel('Product');
        $this->assign('categorys',$categorys);
        $this->display();
    }

    /**
     * 更新
     * @return
     */
    public function update() {
        $productService = D('Product', 'Service');
        $product = $_POST['product'];
        if (!isset($product) || !$productService->existById($product['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        if(empty($_POST['imageType'])){
            return $this->errorReturn('商品没有图片，或至少一张图片设置为主图！');
        }

        $result = $productService->update($product,$_POST['plu_imageIds']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新产品信息成功！', U('Product/index'));
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        $productService = D('Product', 'Service');
        if (!isset($_GET['id']) || !$productService->existById($_GET['id'])) {
            return $this->error('需要删除的产品不存在！');
        }
        $result = $productService->delete($_GET['id']);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn("删除产品成功！");
    }



} 