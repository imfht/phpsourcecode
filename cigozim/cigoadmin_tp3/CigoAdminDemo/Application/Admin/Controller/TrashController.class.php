<?php

namespace Admin\Controller;


use CigoAdminLib\Lib\Admin;
use CigoAdminLib\Lib\SessionCheck;

class TrashController extends SessionCheck
{
    public function index()
    {
        $this->assign('label_title', '回收站');
        $this->display();
    }

    public function getTrashData()
    {
        $model = D('Trash');
        $data = $model->getList($this);

        if ($data) {
            $this->success($data, '', true);
        } else {
            $this->success(array(), '', true);
        }
    }

    public function revertData()
    {
        $dataId = I('get.dataId');
        $type = I('get.type');

        if (empty($dataId) || empty($type)) {
            $this->error('参数错误!');
        }

        switch ($type) {
            case Admin::DATA_TYPE_EDIT_DEMO:
                $model = D('EditDemo');
                break;
            case Admin::DATA_TYPE_MENU_ADMIN:
                $model = D('MenuAdmin');
                break;

            default:
                $this->error('参数错误!');
                return;
        }


        $data = $model->where(array('id' => $dataId))->save(array('status' => 0));
        if ($data) {
            $this->deleteFromTrash($type, $dataId);
            $this->success('恢复成功!');
        } else {
            $this->error('恢复失败!');
        }

    }

    public function removeData()
    {
        $dataId = I('get.dataId');
        $type = I('get.type');

        if (empty($dataId) || empty($type)) {
            $this->error('参数错误!');
        }

        switch ($type) {
            case Admin::DATA_TYPE_MENU_ADMIN:
                $model = D('MenuAdmin');
                break;
            case Admin::DATA_TYPE_EDIT_DEMO:
                $model = D('EditDemo');
                break;

            default:
                $this->error('参数错误!');
                return;
        }

        //TODO 彻底删除，确保子类数据删除，此功能暂时搁置
//		$data = $model->where(array('id' => $dataId))->delete();
        $data = $model->where(array('id' => $dataId))->save(array('status' => -2));
        if ($data) {
            $this->deleteFromTrash($type, $dataId);
            $this->success('删除成功!');
        } else {
            $this->error('删除失败!');
        }
    }
}
