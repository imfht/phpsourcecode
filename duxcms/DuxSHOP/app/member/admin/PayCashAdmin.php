<?php

/**
 * 提现管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayCashAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayCash';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '提现管理',
                'description' => '管理用户提现记录',
            ],
            'fun' => [
                'index' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'C.tel',
            'cash_no' => 'A.cash_no',
            'status' => 'A.status',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time'
        ];
    }

    public function _indexOrder() {
        return 'cash_id desc';
    }

    public function _indexWhere($whereMaps) {
        if($whereMaps['A.status'] > 2) {
            unset($whereMaps['A.status']);
        }
        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }

        if ($startTime) {
            $whereMaps['_sql'][] = 'A.create_time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'A.create_time <= ' . $stopTime;
        }

        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        return $whereMaps;
    }

    public function info() {
        if (!isPost()) {
            $id = request('get', 'id');
            if (empty($id)) {
                $this->error('参数传递错误!');
            }
            $info = target('member/PayCash')->getInfo($id);
            if (empty($info)) {
                $this->error('暂无该记录!');
            }
            $this->assign('info', $info);
            $this->systemDisplay();
        } else {
            $post = request('post');
            $info = target($this->_model)->getInfo($post['cash_id']);
            if (empty($info)) {
                $this->error('暂无该记录!');
            }
            if($info['status'] <> 1) {
                $this->error('该记录已处理!');
            }
            if($info['status'] == 1) {
                $this->error('请选择处理方式!');
            }
            $data = [
                'cash_id' => $post['cash_id'],
                'status' => $post['status'] ? 2 : 0,
                'auth_admin' => $this->userInfo['user_id'],
                'auth_time' => time(),
                'auth_remark' => $post['auth_remark']
            ];
            if($post['status'] == 2) {
                $data['complete_time'] = time();
            }
            target($this->_model)->beginTransaction();

            if (!$post['status']) {
                $status = target('member/Finance', 'service')->account([
                    'user_id' => $info['user_id'],
                    'money' => $info['money'],
                    'pay_no' => log_no(),
                    'pay_name' => '账号支付',
                    'type' => 1,
                    'deduct' => 1,
                    'title' => '提现失败',
                    'remark' => '提现失败退回处理',
                ]);
                if (!$status) {
                    target($this->_model)->rollBack();
                    $this->error(target('member/Finance', 'service')->getError());
                }
            }
            if (!target($this->_model)->edit($data)) {
                target($this->_model)->rollBack();
                $this->error('处理失败,请稍后再试!');
            }
            target($this->_model)->commit();
            $this->success('处理成功!', url('index'));

        }
    }

    public function action() {
        $status = request('post', 'action');
        $ids = request('post', 'ids');
        if(!$status || empty($ids)) {
            $this->error('请选择操作记录!');
        }
        $list = target($this->_model)->loadList(['_sql' => 'cash_id in ('.$ids.')', 'A.status' => 1]);
        if(empty($list)) {
            $this->error('没有可处理记录!');
        }
        $idsArray = [];
        foreach ($list as $vo) {
            $idsArray[] = $vo['cash_id'];
        }
        $data = [
            'status' => 2,
            'auth_admin' => $this->userInfo['user_id'],
            'auth_time' => time(),
        ];
        if($status == 2) {
            $data['complete_time'] = time();
        }
        target($this->_model)->where(['_sql' => 'cash_id in ('.implode(',', $idsArray).')'])->data($data)->update();
        $this->success('提现处理完成!');
    }

    public function export() {
        $params = [
            'C.tel' => request('get', 'keyword'),
            'A.status' => request('get', 'status'),
            'A.cash_no' => request('get', 'cash_no'),
            'start_time' => 'start_time',
            'stop_time' => 'stop_time'
        ];
        $params = array_filter($params);

        $where = $this->_indexWhere($params);

        $list = target($this->_model)->loadList($where);

        if(empty($list)) {
            $this->error('没有数据需要导出!');
        }

        $table = [];
        $table[] = ['流水号', '实付金额', '用户', '提交时间', '开户行', '银行标识', '账户姓名', '账户',  '状态'];

        foreach ($list as $vo) {
            $status = '';
            if(!$vo['status']) {
                $status = '提现失败';
            }
            if($vo['status'] == 1) {
                $status = '提现中';
            }
            if($vo['status'] == 2) {
                $status = '提现完成';
            }
            $data = [$vo['cash_no'], $vo['money'] - $vo['tax_money'], $vo['show_name'],date('Y-m-d H:i:s', $vo['create_time']), $vo['bank'],$vo['bank_label'],  $vo['account_name'],  $vo['account'],  $status];
            foreach ($data as $k => $v) {
                $data[$k] = str_replace(',', '_', $v);
            }
            $table[] = $data;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=data.csv');

        $tableStr = '';
        foreach ($table as $vo) {
            $tableStr .= implode(',', $vo) . PHP_EOL;
        }
        echo iconv('utf-8','gbk//TRANSLIT',$tableStr);
    }

}