<?php

namespace CigoAdminLib\Lib;

use CigoAdminLib\Lib\Uploader\Uploader;

class Admin extends CigoCommon
{
    //性别
    const  SEX_UNKOWN = 0; //保密
    const  SEX_MAN = 1; //男性
    const  SEX_WOMEN = 2; //女性

    //数据分页
    const DATA_LIST_SIZE = 10;

    //专题活动类型
    const SPECIAL_EVENT_TYPE_COMMON = 0;//普通编辑型
    const SPECIAL_EVENT_TYPE_URL = 1;//专题活动页面型

    protected function _initialize()
    {
        parent::_initialize();
        //TODO
        $this->initTmplParseString(false);
    }

    protected function argsError()
    {
        $this->error('参数错误!', U('Index/index'));
    }

    protected function deleteFromTrash($type = 0, $dataId = 0)
    {
        if (empty($type) || empty($dataId)) {
            return;
        }

        $trashModel = D('Trash');
        $trashModel->where(array(
            'data_id' => $dataId,
            'type' => $type
        ))->delete();
    }

	public function upload() {
		if (!IS_POST) {
			$this->error('访问异常!');
		}

		//1. 实例化上传类，并创建文件上传实例
		$upMg = new Uploader();
		if (!$upMg->init()->makeFileUploader()) {
			$this->ajaxReturn($upMg->response());
		}
		//2. 执行上传操作
		$upMg->doUpload();
		$this->ajaxReturn($upMg->response());
	}

	public function imgArgsByToolsCropCommon() {
		$this->display('CigoAdminLib@Common:imgArgsByToolsCropCommon');
	}

	public function imgArgsByManual() {
		$this->display('CigoAdminLib@Common:imgArgsByManual');
	}
}

