<?php
namespace Home\Service;

/**
 * ProductService
 */
class ProductService extends CommonService {
    /**
     * 添加产品
     * @param  array $product 产品信息
     * @return array
     */
    public function add($product,$imageIds) {
        $Product = $this->getD();
        $Product->startTrans();
        if (false === ($product = $Product->create($product))) {
            return $this->errorResultReturn($Product->getError());
        }
        $map = $this->existImageByIds($imageIds);
        $product['has_picture']=$map['has_picture'];
        $product['main_picture']=$map['main_picture'];
        $as = $Product->add($product);
        if (false === $as) {
            $Product->rollback();
            return $this->errorResultReturn('系统出错了！');
        }
        if($map['has_picture']==1){
            D('ProductImage','Service')->updateProductId($imageIds,$as);
        }
        $Product->commit();
        return $this->resultReturn(true);
    }

    /**
     * 更新产品
     * $mainImageId 主图ID
     * @return
     */
    public function update($product,$imageIds)
    {
        $Product = $this->getD();
        if (false === ($product = $Product->create($product))) {
            return $this->errorResultReturn($Product->getError());
        }
        $map = $this->existImageByIds($imageIds);
        $product['has_picture']=$map['has_picture'];
        $product['main_picture']=$map['main_picture'];
        if (false === $Product->save($product)) {
            return $this->errorResultReturn('系统错误！');
        }
        if($map['has_picture']==1){
            D('ProductImage','Service')->updateProductId($imageIds,$product['id']);
        }
        return $this->resultReturn(true);
    }

    /**
     * 删除产品
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        $Product = $this->getD();
        $Product->startTrans();
        $delStatus = $Product->delete($id);
        if (false === $delStatus) {
            $Product->rollback();
            return $this->resultReturn(false);
        }
        $Product->commit();
        return $this->resultReturn(true);
    }

    private function existImageByIds($imageIds){
        $result = D('ProductImage','Service')->existByIds($imageIds);
        if($result['status']){
            $map['has_picture']=1;
            $map['main_picture'] = $result['data']['path'];
        }else{
            $map['has_picture']=2;
            $map['main_picture']=null;
        }
        return $map;
    }

    protected function getModelName() {
        return 'Product';
    }
}
