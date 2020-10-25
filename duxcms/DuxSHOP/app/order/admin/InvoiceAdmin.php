<?php

/**
 * 发票管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class InvoiceAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderInvoice';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '发票管理',
                'description' => '管理订单发票信息',
            ],
            'fun' => [
                'index' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'C.order_no',
            'type' => 'A.status',
        ];
    }

    public function _indexOrder() {
        return 'A.invoice_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['A.status'] > 2) {
            unset($whereMaps['A.status']);
        }
        return $whereMaps;
    }

    public function status() {
        $id = request('', 'id', 0);
        if (empty($id)) {
            if (!isPost()) {
                $this->systemDialogError('参数获取错误!');
            } else {
                $this->error('参数获取错误!');
            }
        }
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            if (!isPost()) {
                $this->systemDialogError('信息获取错误!');
            } else {
                $this->error('信息获取错误!');
            }
        }

        if (!isPost()) {
            $this->assign('info', $info);
            $this->dialogDisplay();
        } else {
            $status = request('post', 'status', 0, 'intval');
            $status = ($status < 3) ? $status : 0;
            $remark = request('post', 'remark', '', 'html_clear');
            if ($status == $info['status']) {
                $this->error('发票状态未改变!');
            }
            target($this->_model)->beginTransaction();
            $data = [];
            $data['invoice_id'] = $id;
            $data['status'] = $status;
            $data['remark'] = $remark;
            $data['process_time'] = time();
            if (!target($this->_model)->edit($data)) {
                target($this->_model)->rollBack();
                $this->error('状态更改失败!');
            }
            target($this->_model)->commit();

            $this->success('状态更改成功!');
        }
    }


}