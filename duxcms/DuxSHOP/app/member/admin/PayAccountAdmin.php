<?php

/**
 * 资金账户管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayAccountAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayAccount';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '资金账户',
                'description' => '查看操作用户资金',
            ],
            'fun' => [
                'index' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.tel,B.email,B.nickname'
        ];
    }

    public function _indexOrder() {
        return 'account_id desc';
    }

    public function operate() {
        if (!isPost()) {
            $id = request('get', 'id');
            if (empty($id)) {
                $this->error('参数传递错误!');
            }
            $info = target($this->_model)->getInfo($id);
            if (empty($info)) {
                $this->error('暂无该记录!');
            }
            $this->assign('info', $info);
            $this->systemDisplay();
        } else {
            $post = request('post');
            if (empty($post['remark'])) {
                $this->error('请输入操作理由!');
            }
            if ($post['type']) {
                $payNo = log_no();
                $data = [];
                $data['user_id'] = $post['user_id'];
                $data['money'] = $post['money'];
                $data['recharge_no'] = $payNo;
                $data['status'] = 0;
                $data['create_time'] = time();
                if(!target('member/PayRecharge')->add($data)) {
                    $this->error('充值订单创建失败!');
                }
                target($this->_model)->beginTransaction();
                $status = target('member/Member', 'service')->payRecharge($payNo, $post['money'], '账号支付', '', '系统充值', $post['remark']);
                if (!$status) {
                    target($this->_model)->rollBack();
                    $this->error(target('member/Member', 'service')->getError());
                }
            } else {
                target($this->_model)->beginTransaction();
                $status = target('member/Finance', 'service')->account([
                    'user_id' => $post['user_id'],
                    'money' => $post['money'],
                    'pay_no' => '',
                    'pay_name' => '账号支付',
                    'type' => 0,
                    'title' => '系统扣除',
                    'remark' => $post['remark']
                ]);
                if (!$status) {
                    target($this->_model)->rollBack();
                    $this->error(target('member/Finance', 'service')->getError());
                }
            }
            target($this->_model)->commit();
            $this->success('账户资金处理成功!', url('index'));
        }
    }

}