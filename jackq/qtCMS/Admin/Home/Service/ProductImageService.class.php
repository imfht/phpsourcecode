<?php
namespace Home\Service;

/**
 *
 */
class ProductImageService extends CommonService {


    public function upload(){
        $uploadInfo = null;
        $uploadDir = C('UPLOAD_ROOT') .'product/';
        // 只执行一次上传
        $uploadInfo = upload($uploadDir);
        if (false === $uploadInfo['status']
            && !empty($uploadInfo['info'])) {
            // 上传失败
            return $this->errorResultReturn($uploadInfo['info']);
        }
        if (true === $uploadInfo['status']
            && !$this->isEmpty($_FILES['file']['tmp_name'])
            && is_array($uploadInfo['info'][0])) {
            // 处理真正上传过的file表单域
            $size = $uploadInfo['info'][0]['size'];
            if (convMb2B(2) < $size) {//最大2M
                // 删除已上传的文件
                foreach ($uploadInfo['info'] as $upload) {
                    // 删除文件
                    unlink(WEB_ROOT . $upload['path']);
                }
                // 超过限制大小
                $msg ="文件大小不能超过2M！";
                return $this->errorResultReturn($msg);
            }
            $img['path'] =  $uploadInfo['info'][0]['path'];
            array_shift($uploadInfo['info']);
            $ProductImage = $this->getD();
            $img = $ProductImage->create($img);
            $id = $ProductImage->add($img);
            $info['path'] = $img['path'];
            $info['id'] = $id;
            return  $this->resultReturn(true,$info);
        }
    }

    /**
     * 删除
     * @param  int $id
     * @return boolean
     */
    public function delete($id)
    {
        $ProductImage = $this->getD();
        $ProductImage->startTrans();
        $delStatus = $ProductImage->delete($id);
        if (false === $delStatus) {
            $ProductImage->rollback();
            return $this->resultReturn(false);
        }
        $ProductImage->commit();
        return $this->resultReturn(true);
    }

    public function changeImageType($id,$allIds)
    {
        $ProductImage = $this->getD();
        $ProductImage->startTrans();
        if($id != $allIds){//先更新所有的图片类型为2
            $data['type']=2;
            $map['id']  = array('in',$allIds);
            $delStatus = $ProductImage->where($map)->save($data);
        }

        $data['type'] = 1;
        $delStatus = $ProductImage->where('id=%d',array($id))->save($data);
        if (false === $delStatus) {
            $ProductImage->rollback();
            return $this->resultReturn(false);
        }
        $ProductImage->commit();
        return $this->resultReturn(true);
    }

    //是否有图片，主图
    //有图片没主图时，默认第一张为主图。
    public function existByIds($ids){
        $ProductImage = $this->getM();
        $map['id']  = array('in',$ids);
        $result =  $ProductImage->where($map)->select();
        if(!empty($result)&& count($result)>0){
            $count = 0;
            foreach($result as $item){
                if($item['type']==1){
                    return $this->resultReturn(true,$item);
                }else{
                    $count++;
                }
            }
            if($count == count($result)){
                return $this->resultReturn(true,$result[0]);
            }

        }
        return $this->resultReturn(false);
    }

    public function updateProductId($ids,$product_id){
        $ProductImage = $this->getM();
        $map['id']  = array('in',$ids);
        $data['product_id'] = $product_id;
        $ProductImage->where($map)->save($data);
    }

    public function findByProductId($product_id){
        $ProductImage = $this->getM();
        $map['product_id']  = $product_id;
        $result =  $ProductImage->where($map)->select();
        return $this->resultReturn(true,$result);
    }

    protected function getModelName() {
        return 'ProductImage';
    }
}
