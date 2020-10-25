<?php

namespace application\modules\dashboard\controllers;

use application\core\utils\Env;
use application\core\utils\File;
use application\core\utils\Ibos;
use application\modules\dashboard\controllers\BaseController;
use application\modules\dashboard\model\LoginTemplate;
use application\modules\dashboard\utils\Dashboard;

class LoginController extends BaseController
{

    public function actionIndex()
    {
        $formSubmit = Env::submitCheck('loginSubmit');
        $bgPath = LoginTemplate::BG_PATH;
        if ($formSubmit) {
            if (isset($_POST['bgs'])) {
                // 更新背景
                foreach ($_POST['bgs'] as $id => $bg) {
                    //移动默认上传到了attachment下面图片到login路径下面，并删除旧文件
                    if(strpos($bg['image'],$bgPath) === false){
                        //复制文件到新路径并将原文件删除
                        $bg['image'] = File::copyToDir($bg['image'], $bgPath, true);
                        //查询原先文件记录并删除旧文件
                        $oldImg = LoginTemplate::model()->findByPk($id);
                        File::deleteFile($oldImg->image);
                    }
                    $bg['disabled'] = isset($bg['disabled']) ? 0 : 1;
                        //修改数据库记录
                        LoginTemplate::model()->modify($id,$bg);
                }
            }
            // 新建背景
            if (isset($_POST['newbgs'])) {
                foreach ($_POST['newbgs'] as $value) {
                    if (!empty($value['image'])) {
                        //移动默认上传到了attachment下面图片到login路径下面，并删除旧文件
                        $value['image'] = File::copyToDir($value['image'],$bgPath, true);
                    }
                    LoginTemplate::model()->add($value);
                }
            }
            // 删除
            if (!empty($_POST['removeId'])) {
                $removeIds = explode(',', trim($_POST['removeId'], ','));
                $oldImg = LoginTemplate::model()->findByPk($removeIds);
                File::deleteFile($oldImg->image);
                LoginTemplate::model()->deleteByIds($removeIds, $bgPath);
            }
            clearstatcache();
            $this->success(Ibos::lang('Save succeed', 'message'));
        } else {
            if (Env::getRequest('op') === 'upload') {
                $fakeUrl = $this->imgUpload('bg');
                $realUrl = File::imageName($fakeUrl);
                return $this->ajaxReturn(array('fakeUrl' => $fakeUrl, 'url' => $realUrl));
            }
            $data = array(
                'list' => LoginTemplate::model()->fetchAll(),
                'bgpath' => $bgPath
            );
            $this->render('index', $data);
        }
    }

}
