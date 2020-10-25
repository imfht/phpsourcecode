<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/1/6
 * Time: 16:59
 */

namespace Home\Controller;


class ProductImageController extends CommonController {

    public function upload(){
        $result = D('ProductImage', 'Service')->upload();
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->successReturn($result['data']);
    }

    public function delete(){
        $id = $_POST['id'];
        if(empty($id)){
            return $this->errorReturn("无效操作！");
        }
        $result =  D('ProductImage', 'Service')->delete($id);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn("删除图片成功！");
    }

    public function changeImageType(){
        $id = $_POST['id'];
        if(empty($id)){
            return $this->errorReturn("无效操作！");
        }
        $allIds = $_POST['allIds'];
        $result =  D('ProductImage', 'Service')->changeImageType($id,$allIds);
        if (false === $result['status']) {
            return $this->errorReturn('系统出错了！');
        }
        $this->successReturn("设置主图成功！");
    }

    public function findByProductId(){
        $productId = $_POST['productId'];
        if(empty($productId)){
            return $this->errorReturn("无效操作！");
        }
        $result = D('ProductImage', 'Service')->findByProductId($productId);
        return $this->successReturn($result['data']);
    }

}