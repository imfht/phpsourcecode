<?php

/**
 * 快递单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class DeliveryAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderDelivery';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '发货管理',
                'description' => '管理订单发货快递',
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
            'keyword' => 'B.order_no',
        ];
    }

    public function _indexOrder() {
        return 'A.delivery_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['A.status'] > 3) {
            unset($whereMaps['A.status']);
        }
        return $whereMaps;
    }

    public function export() {
        if (!isPost()) {
            $this->dialogDisplay();
        } else {
            $post = request('post');
            $startDate = $post['start_date'];
            $stopDate = $post['stop_date'];
            $printStatus = intval($post['print_status']);
            $exportStatus = intval($post['export_status']);
            $changePrint = intval($post['change_print']);
            $changeExport = intval($post['change_export']);
            $remark = request('post', 'remark', '', 'html_clear');
            if (empty($startDate) || empty($stopDate)) {
                $this->error('请选择导出日期!');
            }
            $startDate = strtotime($startDate);
            $stopDate = strtotime($stopDate);
            $stopDate = mktime(23, 59, 59, date("m", $stopDate), date("d", $stopDate), date("Y", $stopDate));
            if ($startDate > $stopDate) {
                $this->error('起始日期不能大于截止日期!');
            }
            $where = [];
            $where['_sql'] = "A.create_time > {$startDate} AND A.create_time < {$stopDate}";
            if ($printStatus == 1) {
                $where['A.print_status'] = 1;
            } elseif ($printStatus == 2) {
                $where['A.print_status'] = 0;
            }
            if ($exportStatus == 1) {
                $where['A.export_status'] = 1;
            } elseif ($exportStatus == 2) {
                $where['A.export_status'] = 0;
            }
            $list = target($this->_model)->loadList($where);
            if (empty($list)) {
                $this->error('暂无导出数据!');
            }
            $ids = [];
            $csv = '';
            foreach ($list as $vo) {
                $ids[] = $vo['delivery_id'];
                $address = str_replace(',', ' ', $vo['receive_address']);
                $csv .= $vo['order_no'] .','. $vo['delivery_name'] . "," . $vo['delivery_no'] . "," . $vo['receive_tel'] . "," . $vo['receive_zip'] . "," . $vo['receive_province'] . "," . $vo['receive_city'] . "," . $vo['receive_region'] . "," . $address . "\n";
            }
            $csv = iconv('utf-8', 'gb2312', $csv);
            if ($changePrint || $changeExport) {
                $data = [];
                if ($changePrint) {
                    $data['print_status'] = 1;
                }
                if ($changeExport) {
                    $data['export_status'] = 1;
                }
                $status = target($this->_model)->where([
                    '_sql' => "delivery_id in (" . implode(',', $ids) . ")"
                ])->data($data)->update();
                if ($status === false) {
                    $this->error('更改导出状态失败!');
                }
            }
            $baseDir = '/upload/csv/';
            $dir = ROOT_PATH . $baseDir;
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0777, true)) {
                    $this->error('请确保"/upload/csv/"目录有权限写入!');
                }
            }
            $config = \dux\Config::get('dux.use');
            $filename = md5(rand() . $config['safe_key']) . '.csv';
            if(!file_put_contents($dir . $filename, $csv)) {
                $this->error('请确保"/upload/csv/"目录有权限写入!');
            }

            $this->success(ROOT_URL . $baseDir . $filename);

        }
    }

    public function printInfo() {
        $id = request('', 'id', 0);
        if (empty($id)) {

            $this->error('参数获取错误!');
        }
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            $this->error('配货信息获取错误!');
        }
        $info['order_items'] = unserialize($info['order_items']);
        $this->assign('info', $info);
        $this->systemDisplay();
    }


}