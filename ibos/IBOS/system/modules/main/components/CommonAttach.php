<?php

/**
 * 通用上传处理
 * @package application.modules.main.components
 * @author banyanCheung <banyan@ibos.com.cn>
 * @version $Id$
 */

namespace application\modules\main\components;

use application\core\components\AttachCore;
use application\core\utils as util;
use application\core\utils\Ibos;
use application\modules\main\model as MainModel;
use CJSON;
use CException;

class CommonAttach extends AttachCore
{

    public function upload()
    {
        $uidTemp = intval(util\Env::getRequest('uid'));
        $uid = empty($uidTemp) ? Ibos::app()->user->uid : $uidTemp;
        // 判断文件类型
        $attach = $this->upload->getAttach();
        if(!$this->checkExt($attach['ext'])){
            $this->isUpload = false;
            $this->errmsg = 'The file type is not in white list';
            throw new CException(Ibos::lang('The file type is not in white list', '',  array('{type}' =>$attach['ext'])));
        }
        $this->upload->save();
        $attachment = $attach['type'] . '/' . $attach['attachment'];
        $data = array(
            'dateline' => TIMESTAMP,
            'filename' => $attach['name'],
            'filesize' => $attach['size'],
            'attachment' => $attachment,
            'isimage' => $attach['isimage'],
            'uid' => $uid
        );
        $aid = MainModel\Attachment::model()->add(array('uid' => $uid, 'tableid' => 127), true);
        $data['aid'] = $aid;
        MainModel\AttachmentUnused::model()->add($data);
        $file['icon'] = util\Attach::attachType($attach['ext']);
        $file['aid'] = $aid;
        $file['name'] = $attach['name'];
        $file['url'] = util\File::fileName(util\File::getAttachUrl() . '/' . $attachment, false);
        $attach['aid'] = $aid;
        $this->upload->setAttach($attach);
        if (!empty($file) && is_array($file)) {
            $this->isUpload = true;
            return CJSON::encode($file);
        } else {
            $this->isUpload = false;
            $this->errmsg = 'Upload failed';
            return CJSON::encode(array('aid' => 0, 'url' => 0, 'name' => 0));
        }
    }

    public function getUpload()
    {
        return $this->upload;
    }

    public function getIsUpoad()
    {
        return $this->isUpload;
    }

    /**
     * 获取上传附件大小
     * @return integer
     */
    public function getAttachSize()
    {
        $attach = $this->upload->getAttach();
        $size = isset($attach['size']) ? intval($attach['size']) : 0;
        return $size;
    }

    /**
     * 更新附件到附件表，从“未使用”表移除
     * @param mixed $attachids
     * @param string $related
     */
    public function updateAttach($attachids, $related = 0, $isFromUploadFile=false)
    {
        return util\Attach::updateAttach($attachids, $related, $isFromUploadFile);
    }

    /**
     * 上传文件检查后缀名是否为不可上传的类型
     */
    private function checkExt($ext){
        $ext = strtoupper($ext);

        // 黑名单
        $blackList = array(
            'PHP', // 防止脚本攻击
        );

        if(in_array($ext, $blackList)) {
            return false; // 黑名单即使定义能上传也不予许上传
        }

        // 必须上传的白名单,定义时请大写
        $whiteList = array(
            'XML',  // 同步IM
            'XLS',  // 导入
            'XLSX', // 导入
            'HTM',  // 工作流导入模板
            'MTML', // 工作流导入模板
            'CSV',  // 导入
        );

        if(in_array($ext, $whiteList)) {
            return true; // 系统重要文件格式允许上传
        }

        $setting = preg_replace("/(\s|　|\xc2\xa0)/","", Ibos::app()->setting->get('setting/filetype')); //去除所有空格
        $disallowExt = explode(',', strtoupper($setting));
        if(!in_array($ext, $disallowExt)) {
            return false;
        }
        return true;
    }

    /*
     * 获取上传失败信息
     */
    public function getUploadErrMsg() {
        // 这里不给出具体返回语言只固定标识，方便文案自定义
        // 如果需要修改文案，请在模块的语言包内定义相同键名的文案
        return $this->errmsg;
    }
}
