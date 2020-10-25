<?php

/**
 * main模块的附件控制器
 *
 * @version $Id$
 * @package application.modules.main.controllers
 */

namespace application\modules\main\controllers;

use application\core\controllers\Controller;
use application\core\engines\saas\SaasFile2;
use application\core\utils as util;
use application\core\utils\Api;
use application\core\utils\File;
use application\modules\file\core\FileLocal;
use application\modules\file\core\FileOperationApi;
use application\modules\main\model\AttachmentN;
use application\modules\main\model\AttachmentUnused;
use application\core\utils\Ibos;
use application\modules\main\model\Setting;
use CURLFile;
use Yii;

class AttachController extends Controller
{

    private $api = array(
        'upload' => "http://www.yozodcs.com/upload",
        'convert' => "http://www.yozodcs.com/convert",
    );

    /**
     * 上传控制器
     * @return mixed
     */
    public function actionUpload()
    {
        //会议管理》会议申请》文件上传 的bug
        //检查$_FILES是否为空，检查$_FILES['Filedata']['error']是否非0
        if (empty($_FILES) || $_FILES['Filedata']['error'] != 0) {
            //TODO 这里不知道返回什么错误提示
            $echo = array('icon' => '', 'aid' => -1, 'name' => '上传失败', 'url' => '');
            $this->ajaxReturn(json_encode($echo), 'eval');
        }

        // 安全验证
        // 附件类型，可指定可不指定，不指定为普通类型
        $attachType = util\Env::getRequest('type');
        if (empty($attachType)) {
            $attachType = 'common';
        }
        $module = util\Env::getRequest('module');
        $object = '\application\modules\main\components\CommonAttach';
        if (class_exists($object)) {
            $attach = new $object('Filedata', $module);
            $return = $attach->upload();
            $this->ajaxReturn($return, 'eval');
        }
    }

    /*
     * 获取上传弹框视图
     */

    public function actionGetView()
    {
        $alias = 'application.views.upload';
        $views = $this->renderPartial($alias, array(), true);
        echo $views;
    }

    /**
     * 下载控制
     * @return type
     */
    public function actionDownload()
    {
        $data = $this->getData();
        $data['attach']['attachment'] = util\File::getAttachUrl() . '/' . $data['attach']['attachment'];
        if (!empty($data)) {
            return util\File::download($data['attach'], $data['decodeArr']);
        }
        $this->setPageTitle(util\Ibos::lang('Filelost'));
        $this->setPageState('breadCrumbs', array(
            array('name' => util\Ibos::lang('Filelost'))
        ));
        $this->render('filelost');
    }

    /**
     * office 在线预览
     */
    public function actionOffice()
    {
        // 获取设置
        $officePreview = Setting::model()->fetchSettingValueByKey('officepreview'); // office 预览方式
        $officePreview = empty($officePreview) ? 0 : $officePreview; // 默认为普通的ie插件模式
        $op = util\Env::getRequest('op'); // 如果参数为修改的话不能用永中
        if ($officePreview == 0 || $op == 'edit') {
            // 之前ie插件预览
            if (util\Env::submitCheck('formhash')) {
                $widget = util\Ibos::app()->getWidgetFactory()->createWidget($this, 'application\modules\main\widgets\Office', array());
                echo $widget->handleRequest();
            } else {
                $data = $this->getData();
                $data['decodeArr']['op'] = util\Env::getRequest('op');
                $widget = $this->createWidget('application\modules\main\widgets\Office', array('param' => $data['decodeArr'], 'attach' => $data['attach']));
                echo $widget->run();
            }
        } else if ($officePreview == 1 && $op == 'read') {
            $data = $this->getData();
            // 永中在线预览
            if (ENGINE === "LOCAL") {
                $ret = $this->localOfficePreview($data);
            } else if (ENGINE === "SAAS") {
                $ret = $this->saasOfficePreview($data);
            }
            $ret = json_decode($ret, true);
            if (!empty($ret) && $ret['result'] == 0 && !empty($ret['data'][0])) {
                header('Location: ' . $ret['data'][0]);
            } else {
                $this->error('在线预览错误');
            }
        }
    }

    /**
     * 本地上传文件给永中，预览
     * @param $data
     * @return bool
     */
    private function localOfficePreview($data)
    {
        $path = realpath(PATH_ROOT . '/' . File::getAttachUrl() . '/' . $data['attach']['attachment']);
        $filePathInfo = pathinfo($path);
        $post = array("convertType" => 1);
        $mime = Api::getInstance()->getFileMime($filePathInfo['extension']);
        if (@class_exists('\CURLFile')) {
            $post['file'] = new CURLFile($path, $mime, $filePathInfo['filename']);
        } else {
            $post['file'] = "@" . $path . ";type=" . $mime . ";filename=" . $filePathInfo['filename'];
        }
        $result = Api::getInstance()->fetchResult($this->api['upload'], $post, 'post');
        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    /**
     * saas用给永中服务器下载链接然后解析的方案
     * @param $data
     */
    private function saasOfficePreview($data)
    {
        $fileContent = File::readFile(File::getAttachUrl() . '/' . $data['attach']['attachment']);
        // 生成临时文件
        $tempName = tempnam(Yii::app()->getRuntimePath(), 'TPM');
        $temp = fopen($tempName, 'w');
        fwrite($temp, $fileContent);
        fclose($temp);
        $filePathInfo = pathinfo($tempName);
        $post = array("convertType" => 1);
        $mime = Api::getInstance()->getFileMime($filePathInfo['extension']);
        if (@class_exists('\CURLFile')) {
            $post['file'] = new CURLFile($tempName, $mime, $filePathInfo['filename']);
        } else {
            $post['file'] = "@" . $tempName . ";type=" . $mime . ";filename=" . $filePathInfo['filename'];
        }
        $result = Api::getInstance()->fetchResult($this->api['upload'], $post, 'post');
        // 删除临时文件
        unlink($tempName);
        if (!empty($result)) {
            return $result;
        }
        return false;
    }

    private function getData()
    {
        $id = util\Env::getRequest('id');
        $aidString = base64_decode(rawurldecode($id));
        if (empty($aidString)) {
            $this->error(util\Ibos::lang('Parameters error', 'error'), '', array('autoJump' => 0));
        }
        // 解码
        $salt = util\Ibos::app()->user->salt;
        $decodeString = util\StringUtil::authCode($aidString, 'DECODE', $salt);
        $decodeArr = explode('|', $decodeString);
        $count = count($decodeArr);
        if ($count < 3) {
            $this->error(util\Ibos::lang('Data type invalid', 'error'), '', array('autoJump' => 0));
        } else {
            $aid = $decodeArr[0];
            $tableId = $decodeArr[1];
            if ($tableId >= 0 && $tableId < 10) {
                $attach = AttachmentN::model()->fetch($tableId, $aid);
            }
            $return = array('decodeArr' => $decodeArr, 'attach' => array());
            if (!empty($attach)) {
                $return['attach'] = $attach;
            }
            return $return;
        }
    }

    /**
     * 上传base64的图片
     */
    public function actionUploadBase()
    {
        $imgObject = util\Env::getRequest('img');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $imgObject, $result)) {
            $fileName = 'data/attachment/' . util\Ibos::getCurrentModuleName() . '/' . md5(time()) . 'jpg';
            $fileContent = base64_decode(str_replace($result[1], '', $imgObject));
            if (!is_dir('data/attachment/' . util\Ibos::getCurrentModuleName())) {
                mkdir('data/attachment/' . util\Ibos::getCurrentModuleName(), 777);
            }
            File::createFile($fileName, $fileContent);
            $uploadUrl = File::fileName($fileName);
        } else {
            $uploadUrl = '';
        }
        // H5端签章图片路径需要全路径，传递host去拼接，saas保存的是全路径，所以不需要。
        $host = Ibos::app()->request->getHostInfo() . '/';
        $this->ajaxReturn(array(
            'isSuccess' => empty($uploadUrl) ? false : true,
            'msg' => empty($uploadUrl) ? '上传失败' : '上传成功',
            'data' => $uploadUrl,
            'host' => strtolower(ENGINE) == 'saas' ? '' : $host
        ));
    }
}
