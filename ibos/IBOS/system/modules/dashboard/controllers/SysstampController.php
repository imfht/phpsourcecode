<?php

namespace application\modules\dashboard\controllers;

use application\core\utils\Env;
use application\core\utils\File;
use application\core\utils\Ibos;
use application\modules\dashboard\model\Stamp;
use application\modules\dashboard\utils\Dashboard;

class SysstampController extends BaseController
{

    public function actionIndex()
    {
        $formSubmit = Env::submitCheck('stampSubmit');
        $stampPath = Stamp::STAMP_PATH;
        if ($formSubmit) {
            if (isset($_POST['stamps'])) {
                // 更新图章
                foreach ($_POST['stamps'] as $id => $stamp) {

                    //更新图章 - 移动默认上传到了attachment下面图片到login路径下面，并删除旧文件
                    if(strpos($stamp['stamp'],$stampPath) === false){
                        //复制文件到新路径并将原文件删除
                        $stamp['stamp'] = File::copyToDir($stamp['stamp'], $stampPath, true);
                        //查询原先文件记录并删除旧文件
                        $oldImg = Stamp::model()->findByPk($id);
                        File::deleteFile($oldImg->stamp);
                    }

                    //更新图标 - 移动默认上传到了attachment下面图片到login路径下面，并删除旧文件
                    if(strpos($stamp['icon'],$stampPath) === false){
                        //复制文件到新路径并将原文件删除
                        $stamp['icon'] = File::copyToDir($stamp['icon'], $stampPath, true);
                        //查询原先文件记录并删除旧文件
                        $oldImg = Stamp::model()->findByPk($id);
                        File::deleteFile($oldImg->icon);
                    }

                    $stamp['code'] = \CHtml::encode($stamp['code']);
                    //修改数据库记录
                    Stamp::model()->modify($id,$stamp);

                }
            }
            // 新建图章与图标
            if (isset($_POST['newstamps'])) {
                foreach ($_POST['newstamps'] as $value) {
                    //新建图章
                    if (!empty($value['stamp'])) {
                        $oldImg = $value['stamp'];
                        //移动默认上传到了attachment下面图片到login路径下面，并删除旧文件
                        $value['stamp'] = File::copyToDir($value['stamp'],$stampPath, true);
                        //删除旧文件
                        File::deleteFile($oldImg);
                    }
                    //新建图标
                    if (!empty($value['icon'])) {
                        $oldImg = $value['icon'];
                        //移动默认上传到了attachment下面图片到login路径下面，并删除旧文件
                        $value['icon'] = File::copyToDir($value['icon'],$stampPath, true);
                        //删除旧文件
                        File::deleteFile($oldImg);
                    }

                    Stamp::model()->add($value);

                }
            }
            // 删除
            if (!empty($_POST['removeId'])) {
                $removeIds = explode(',', trim($_POST['removeId'], ','));
                $oldImg = Stamp::model()->findByPk($removeIds);
                File::deleteFile($oldImg->stamp);
                File::deleteFile($oldImg->icon);
                Stamp::model()->deleteByIds($removeIds);
            }
            clearstatcache();
            $this->success(Ibos::lang('Save succeed', 'message'));
        } else {
            if (Env::getRequest('op') === 'upload') {
                $fakeUrl = $this->imgUpload('stamp');
                $realUrl = File::fileName($fakeUrl);
                return $this->ajaxReturn(array('fakeUrl' => $fakeUrl, 'url' => $realUrl));
            }
            $data = array(
                'stampUrl' => $stampPath,
                'list' => Stamp::model()->fetchAll(),
                'maxSort' => Stamp::model()->getMaxSort()
            );
            $this->render('index', $data);
        }
    }

}
