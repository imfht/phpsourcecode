<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:52
 */

namespace Home\Service;


class SiteinfoService extends CommonService {

    public function  getSiteinfo(){
        $Siteinfo = $this->getM();
        return $Siteinfo->limit(1)->select();
    }

    public function saveOrUpdate($siteinfo){
        $oldImg = $siteinfo['twocode'];
        $once = false;
        $uploadInfo = null;
        $uploadDir = C('UPLOAD_ROOT') .'twocode/';
        if (!$once) {
            // 只执行一次上传
            $uploadInfo = upload($uploadDir);
            if (false === $uploadInfo['status']
                && !empty($uploadInfo['info'])) {
                // 上传失败
                return $this->errorResultReturn($uploadInfo['info']);
            }
            $once = true;
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

            $siteinfo['twocode'] = $uploadInfo['info'][0]['path'];
            array_shift($uploadInfo['info']);

            //删除已有的图片
            if(!empty($oldImg)){
                unlink(WEB_ROOT . $oldImg);
            }
        }

        $Siteinfo = $this->getD();
        $siteinfo = $Siteinfo->create($siteinfo);
        if(empty($siteinfo['id'])){
            $Siteinfo->add($siteinfo);
        }else {
            $Siteinfo->save($siteinfo);
        }
        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Siteinfo';
    }

    
    

} 