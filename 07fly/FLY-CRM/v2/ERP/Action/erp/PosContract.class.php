<?php

/*
 *
 * crm.Contract  客户销售合同管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */

class PosContract extends Action
{
    private $cacheDir = '';//缓存目录

    public function __construct()
    {
        _instance('Action/sysmanage/Auth');
        $this->sys_user = _instance('Action/sysmanage/User');
        $this->dict = _instance('Action/crm/CstDict');
        $this->supplier = _instance('Action/erp/SupSupplier');
        $this->linkman = _instance('Action/erp/SupLinkman');
        //$this->PosContractDetail  = _instance('Action/PosContractDetail');
    }

    public function pos_contract()
    {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize = $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum) ? 1 : $pageNum;
        $pageSize = empty($pageSize) ? $GLOBALS["pageSize"] : $pageSize;

        //**************************************************************************
        //**获得传送来的数据做条件来查询
        $keywords = $this->_REQUEST("keywords");
        $supplier_id = $this->_REQUEST("supplier_id");
        $supplier_name = $this->_REQUEST("supplier_name");
        $salestage = $this->_REQUEST("salestage");
        $start_date = $this->_REQUEST("start_date");
        $end_date = $this->_REQUEST("end_date");

        $where_str = "s.supplier_id=c.supplier_id";

        if (!empty($keywords)) {
            $where_str .= " and (s.name like '%$keywords%' or s.mobile like '%$keywords%' or s.tel like '%$keywords%')";
        }
        if (!empty($supplier_id)) {
            $where_str .= " and s.supplier_id='$supplier_id'";
        }
        if (!empty($address)) {
            $where_str .= " and s.address like '%$address%'";
        }
        if (!empty($supplier_name)) {
            $where_str .= " and c.name like '%$supplier_name%'";
        }
        //到期时间
        if (!empty($start_date)) {
            switch ($start_date) {
                case '3d' :
                    $date_range = date('Y-m-d', strtotime("-3 day", time()));
                    break;
                case '7d' :
                    $date_range = date('Y-m-d', strtotime("-7 day", time()));
                    break;
                case '15d' :
                    $date_range = date('Y-m-d', strtotime("-15 day", time()));
                    break;
                case '1m' :
                    $date_range = date('Y-m-d', strtotime("-1 month", time()));
                    break;
                case '3m' :
                    $date_range = date('Y-m-d', strtotime("-3 month", time()));
                    break;
                case '6m' :
                    $date_range = date('Y-m-d', strtotime("-6 month", time()));
                    break;
                case '12m' :
                    $date_range = date('Y-m-d', strtotime("-12 month", time()));
                    break;

            }
            $where_str .= " and s.start_date>'$date_range'";
        }
        //到期时间
        if (!empty($end_date)) {
            switch ($end_date) {
                case '3d' :
                    $date_range = date('Y-m-d', strtotime("+3 day", time()));
                    break;
                case '7d' :
                    $date_range = date('Y-m-d', strtotime("+7 day", time()));
                    break;
                case '15d' :
                    $date_range = date('Y-m-d', strtotime("+15 day", time()));
                    break;
                case '1m' :
                    $date_range = date('Y-m-d', strtotime("+1 month", time()));
                    break;
                case '3m' :
                    $date_range = date('Y-m-d', strtotime("+3 month", time()));
                    break;
                case '6m' :
                    $date_range = date('Y-m-d', strtotime("+6 month", time()));
                    break;
                case '12m' :
                    $date_range = date('Y-m-d', strtotime("+12 month", time()));
                    break;

            }
            $where_str .= " and s.end_date<'$date_range'";
        }

        //排序操作
        $orderField = $this->_REQUEST("orderField");
        $orderDirection = $this->_REQUEST("orderDirection");
        $order_by = "order by";
        if ($orderField == 'by_supplier') {
            $order_by .= " s.supplier_id $orderDirection";
        } else if ($orderField == 'by_startdate') {
            $order_by .= " s.start_date $orderDirection";
        } else if ($orderField == 'by_enddate') {
            $order_by .= " s.end_date $orderDirection";
        } else if ($orderField == 'by_money') {
            $order_by .= " s.money $orderDirection";
        } else if ($orderField == 'by_backmoney') {
            $order_by .= " s.pay_money $orderDirection";
        } else if ($orderField == 'by_owemoney') {
            $order_by .= " s.owe_money $orderDirection";
        } else {
            $order_by .= " s.contract_id desc";
        }
        //**************************************************************************

        $moneySql = "select sum(s.money) as total_money,
								sum(s.pay_money) as total_pay_money,
								sum(s.zero_money) as total_zero_money,
								sum(s.pay_money) as total_pay_money,
								sum(s.owe_money) as total_owe_money
						 from pos_contract as s,sup_supplier as c where $where_str";
        $moneyRs = $this->C($this->cacheDir)->findOne($moneySql);

        $countSql = "select s.* from pos_contract as s,sup_supplier as c where $where_str";
        $totalCount = $this->C($this->cacheDir)->countRecords($countSql);
        $beginRecord = ($pageNum - 1) * $pageSize;//计算开始行数
        $sql = "select s.*,c.name as supplier_name from pos_contract as s,sup_supplier as c
						where  $where_str $order_by limit $beginRecord,$pageSize";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $operate = array();
        foreach ($list as $key => $row) {
            $list[$key]['linkman'] = $this->linkman->sup_linkman_get_one($row['linkman_id']);
            $list[$key]['status_arr'] = $this->pos_contract_status($row['status']);
            $list[$key]['rece_status_arr'] = $this->pos_contract_rece_status($row['rece_status']);
            $list[$key]['invoice_status_arr'] = $this->pos_contract_invoice_status($row['invoice_status']);
            $list[$key]['pay_status_arr'] = $this->pos_contract_pay_status($row['pay_status']);
            $list[$key]['our_user_arr'] = $this->sys_user->user_get_one($row['our_user_id']);
        }

        $assignArray = array('list' => $list, "pageSize" => $pageSize, "totalCount" => $totalCount, "pageNum" => $pageNum, "countMoney" => $moneyRs);
        return $assignArray;
    }

    public function pos_contract_json()
    {
        $assArr = $this->pos_contract();
        echo json_encode($assArr);
    }

    //合同显示
    public function pos_contract_show()
    {
        $smarty = $this->setSmarty();
        //$smarty->assign($assArr);
        $smarty->display('erp/pos_contract_show.html');
    }

    //查看一条合同详细
    public function pos_contract_detail()
    {
        $contract_id = $this->_REQUEST("contract_id");
        $one = $this->pos_contract_get_one($contract_id);
        $one['supplier'] = $this->supplier->sup_supplier_get_one($one['supplier_id']);
        $one['linkman'] = $this->linkman->sup_linkman_get_one($one['linkman_id']);
        $one['status_arr'] = $this->pos_contract_status($one['status']);
        $one['invoice_status_arr'] = $this->pos_contract_invoice_status($one['invoice_status']);
        $one['pay_status_arr'] = $this->pos_contract_pay_status($one['pay_status']);
        $one['our_user_arr'] = $this->sys_user->user_get_one($one['our_user_id']);
        $smarty = $this->setSmarty();
        $smarty->assign(array("one" => $one));
        $smarty->display('erp/pos_contract_detail.html');
    }

    //合同添加
    public function pos_contract_add()
    {
        $supplier_id = $this->_REQUEST("supplier_id");
        if (empty($_POST)) {
            $supplier = $this->supplier->sup_supplier_list();
            $sys_user = $this->sys_user->user_list();
            $number = date("ymdH") . rand(10, 99);
            $smarty = $this->setSmarty();
            $smarty->assign(array("number" => $number, "supplier_id" => $supplier_id, "supplier" => $supplier, "sys_user" => $sys_user));
            $smarty->display('erp/pos_contract_add.html');
        } else {
            $into_data = array(
                'contract_no' => $this->_REQUEST("contract_no"),
                'supplier_id' => $this->_REQUEST("supplier_id"),
                'linkman_id' => $this->_REQUEST("linkman_id"),
                'chance_id' => $this->_REQUEST("chance_id"),
                'start_date' => $this->_REQUEST("start_date"),
                'end_date' => $this->_REQUEST("end_date"),
                'title' => $this->_REQUEST("title"),
                'money' => $this->_REQUEST("money"),
                'owe_money' => $this->_REQUEST("money"),
                'our_user_id' => $this->_REQUEST("our_user_id"),
                'intro' => $this->_REQUEST("intro"),
                'create_time' => NOWTIME,
                'create_user_id' => SYS_USER_ID,
            );
            if ($this->C($this->cacheDir)->insert('pos_contract', $into_data)) {
                $this->L("Common")->ajax_json_success("操作成功");
            }
        }
    }

    //查询一条记录
    public function pos_contract_get_one($contract_id = "")
    {
        $sql = "select * from pos_contract where contract_id='$contract_id'";
        $one = $this->C($this->cacheDir)->findOne($sql);
        return $one;
    }

    //查询一条记录
    public function pos_contract_get_one_json()
    {
        $contract_id = $this->_REQUEST("contract_id");
        $one = $this->pos_contract_get_one($contract_id);
        echo json_encode($one);
    }

    //修改
    public function pos_contract_modify()
    {
        $contract_id = $this->_REQUEST("contract_id");
        if (empty($_POST)) {
            $sql = "select * from pos_contract where contract_id='$contract_id'";
            $one = $this->C($this->cacheDir)->findOne($sql);
            $supplier = $this->supplier->sup_supplier_list();
            $sys_user = $this->sys_user->user_list();
            $smarty = $this->setSmarty();
            $smarty->assign(array("one" => $one, "supplier" => $supplier, "sys_user" => $sys_user));
            $smarty->display('erp/pos_contract_modify.html');

        } else {//更新保存数据
            $into_data = array(
                'contract_no' => $this->_REQUEST("contract_no"),
                'supplier_id' => $this->_REQUEST("supplier_id"),
                'linkman_id' => $this->_REQUEST("linkman_id"),
                'chance_id' => $this->_REQUEST("chance_id"),
                'start_date' => $this->_REQUEST("start_date"),
                'end_date' => $this->_REQUEST("end_date"),
                'title' => $this->_REQUEST("title"),
                'money' => $this->_REQUEST("money"),
                'owe_money' => $this->_REQUEST("money"),
                'our_user_id' => $this->_REQUEST("our_user_id"),
                'intro' => $this->_REQUEST("intro"),
                'create_time' => NOWTIME,
                'create_user_id' => SYS_USER_ID,
            );
            if ($this->C($this->cacheDir)->modify('pos_contract', $into_data, "contract_id='$contract_id'")) {
                $this->L("Common")->ajax_json_success("操作成功");
            }
        }
    }

    //订单删除
    public function pos_contract_del()
    {
        $contract_id = $this->_REQUEST("contract_id");
        $this->C($this->cacheDir)->delete('pos_contract', "contract_id in ($contract_id)");
        $this->C($this->cacheDir)->delete('pos_contract_list', "contract_id in ($contract_id)");
        $this->L("Common")->ajax_json_success("操作成功");
    }

    //下拉选择回放数据,显示未付款，及部分的合同
    public function pos_contract_select_pay()
    {
        $supplier_id = $this->_REQUEST("supplier_id");
        $where_str = "and pay_status in(1,2)";
        $sql = "select contract_id,title	from pos_contract where supplier_id='$supplier_id' $where_str order by contract_id asc;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        echo json_encode($list);
    }

    //拉选择回放数据,显示未付款，及部分的合同
    public function pos_contract_select_invoice()
    {
        $supplier_id = $this->_REQUEST("supplier_id");
        $where_str = "and invoice_status in(1,2)";
        $sql = "select contract_id,title from pos_contract where supplier_id='$supplier_id' $where_str order by contract_id asc;";
        $list = $this->C($this->cacheDir)->findAll($sql);
        echo json_encode($list);
    }

    //审核
    public function pos_contract_audit()
    {
        $id = $this->_REQUEST("id");
        $status = $this->_REQUEST("status");
        $sql = "update pos_contract set status='$status' where id='$id'";
        $this->C($this->cacheDir)->update($sql);
        $this->L("Common")->ajax_json_success("操作成功");
    }

    //合同状态
    public function pos_contract_status($key = null)
    {
        $data = array(
            "1" => array(
                'status_name' => '临时单',
                'status_name_html' => '<span class="label label-warning">临时单<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'detail',
                        'color' => '#7266BA',
                        'name' => '详细'
                    ),
                    '1' => array(
                        'act' => 'modify',
                        'color' => '#23B7E5',
                        'name' => '修改合同'
                    ),
                    '2' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    ),
                ),
            ),
            "2" => array(
                'status_name' => '执行中',
                'status_name_html' => '<span class="label label-info">执行中<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'detail',
                        'color' => '#7266BA',
                        'name' => '详细'
                    ),
                    '1' => array(
                        'act' => 'list_detail',
                        'color' => '#23B7E5',
                        'name' => '查看清单'
                    )
                ),
            ),
            "3" => array(
                'status_name' => '完成',
                'status_name_html' => '<span class="label label-success">完成<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'detail',
                        'color' => '#7266BA',
                        'name' => '详细'
                    ),
                    '1' => array(
                        'act' => 'list_detail',
                        'color' => '#23B7E5',
                        'name' => '查看清单'
                    )
                ),
            ),
            "4" => array(
                'status_name' => '撤销',
                'status_name_html' => '<span class="label label-danger">撤销<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'detail',
                        'color' => '#7266BA',
                        'name' => '详细'
                    )
                ),
            )
        );
        return ($key) ? $data[$key] : $data;
    }


    //付款状态
    public function pos_contract_pay_status($key = null)
    {
        $data = array(
            "1" => array(
                'status_name' => '未付',
                'color' => '#FAD733',
                'status_name_html' => '<span class="label label-info">未付<span>',
            ),
            "2" => array(
                'status_name' => '部分',
                'color' => '#23B7E5',
                'status_name_html' => '<span class="label label-info">部分<span>',
            ),
            "3" => array(
                'status_name' => '已付',
                'color' => '#27C24C',
                'status_name_html' => '<span class="label label-info">已付<span>',
            )
        );
        return ($key) ? $data[$key] : $data;
    }

    //收货状态
    public function pos_contract_rece_status($key = null)
    {
        $data = array(
            "1" => array(
                'status_name' => '需要',
                'status_name_html' => '<span class="label label-warning">需要<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'list_add',
                        'color' => '#23B7E5',
                        'name' => '录入明细'
                    )
                ),
            ),
            "2" => array(
                'status_name' => '已录明细',
                'status_name_html' => '<span class="label label-primary">已录明细<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'stock_into',
                        'color' => '#7266BA',
                        'name' => '生成入库单'
                    ),
                    '1' => array(
                        'act' => 'list_del',
                        'color' => '#F05050',
                        'name' => '删除明细'
                    )
                ),
            ),
            "3" => array(
                'status_name' => '待入库',
                'status_name_html' => '<span class="label label-danger">待入库<span>',
                'status_operation' => array(),
            ),
            "4" => array(
                'status_name' => '部分',
                'status_name_html' => '<span class="label label-info">部分<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'stock_into',
                        'color' => '#23B7E5',
                        'name' => '生成入库单'
                    ),
                ),
            ),
            "5" => array(
                'status_name' => '全部',
                'status_name_html' => '<span class="sucess">全部<span>',
                'status_operation' => array(),
            )
        );
        return ($key) ? $data[$key] : $data;
    }

    //收票状态
    public function pos_contract_invoice_status($key = null)
    {
        $data = array(
            "0" => array(
                'status_name' => '不需要',
                'color' => '#7266BA',
                'status_name_html' => '<span class="label label-info">需要<span>',
            ),
            "1" => array(
                'status_name' => '需要',
                'color' => '#FAD733',
                'status_name_html' => '<span class="label label-info">需要<span>',
            ),
            "2" => array(
                'status_name' => '部分',
                'color' => '#23B7E5',
                'status_name_html' => '<span class="label label-info">部分<span>',
            ),
            "3" => array(
                'status_name' => '全部',
                'color' => '#27C24C',
                'status_name_html' => '<span class="label label-info">全部<span>',
            )
        );
        return ($key) ? $data[$key] : $data;
    }


    //付款修改合同付付款状态功能
    public function pos_contract_modify_status($contract_id)
    {
        $one = $this->pos_contract_get_one($contract_id);
        $pay_status = $one['pay_status'];
        $invoice_status = $one['invoice_status'];
        $rece_status = $one['rece_status'];
        //回款，发票，都开完了
        if ($pay_status == '3' && $invoice_status == '3' && $rece_status == '5') {
            $status = '3';//完成状态
        } else if ($pay_status == '1' && $invoice_status == '1' && $rece_status == '1') {
            $status = '1';//临时状态
        } else {
            $status = '2';
        }
        $upd_date = array('status' => $status);
        $this->C($this->cacheDir)->modify('pos_contract', $upd_date, "contract_id='$contract_id'");
        return true;
    }

    //入库之后改合同付收货状态功能
    public function pos_contract_modify_rece_status($contract_id)
    {
        $totalSql = "select sum(owe_num) as total_owe_num,sum(into_num) as total_into_num,sum(num) as total_num
				   from pos_contract_list  where contract_id='$contract_id'";
        $totalRs = $this->C($this->cacheDir)->findOne($totalSql);

        if ($totalRs['total_owe_num'] > 0) {
            $rece_status = '4';
        } else if ($totalRs['total_owe_num'] == 0) {
            $rece_status = '5';
        }
        if ($totalRs['total_into_num'] == 0) {
            $rece_status = '2';
        }
        if ($totalRs['total_num'] == 0) {
            $rece_status = '1';
        }

        $this->C($this->cacheDir)->modify('pos_contract', array('rece_status' => $rece_status), "contract_id='$contract_id'");
        return true;
    }

}

?>