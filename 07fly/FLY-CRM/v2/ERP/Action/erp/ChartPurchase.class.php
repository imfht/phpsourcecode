<?php

/*
 * epr.ChartPurchase  按销售汇总统计
 *
 * @copyright   Copyright (C) 2017-2028 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */

class ChartPurchase extends Action
{
    private $cacheDir = '';//缓存目录

    public function __construct()
    {
        _instance('Action/sysmanage/Auth');

    }

    public function chart_purchase($id = null)
    {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize = $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum) ? 1 : $pageNum;
        $pageSize = empty($pageSize) ? '1000' : $pageSize;

        $keywords = $this->_REQUEST("keywords");
        $pay_status = $this->_REQUEST("pay_status");
        $status = $this->_REQUEST("status");

        $date_type = $this->_REQUEST("date_type");
        $date_b = $this->_REQUEST("date_b");
        $date_e = $this->_REQUEST("date_e");

        $where_str = " create_user_id in (" . SYS_USER_ID . "," . SYS_USER_SUB_ID . ")";

        if (!empty($pay_status)) {
            $where_str .= " and pay_status='$pay_status'";
        }
        if (!empty($status)) {
            $where_str .= " and status='$status'";
        }

        //到期时间
        if (!empty($date_type)) {
            switch ($date_type) {
                case '1' :
                    if (!empty($date_b)) {
                        $where_str .= " and create_time>='$date_b'";
                    }
                    if (!empty($date_e)) {
                        $where_str .= " and create_time<'$date_e'";
                    }
                    break;
                case '2' :
                    if (!empty($date_b)) {
                        $where_str .= " and start_date>='$date_b'";
                    }
                    if (!empty($date_e)) {
                        $where_str .= " and start_date<'$date_e'";
                    }
                    break;
                case '3' :
                    if (!empty($date_b)) {
                        $where_str .= " and end_date>='$date_b'";
                    }
                    if (!empty($date_e)) {
                        $where_str .= " and end_date<'$date_e'";
                    }
                    break;

            }
        }
        //排序操作
        $orderField = $this->_REQUEST("orderField");
        $orderDirection = $this->_REQUEST("orderDirection");
        $order_by = "order by";
        if ($orderField == 'by_total_money') {
            $order_by .= " sa.total_money $orderDirection";
        } else if ($orderField == 'by_total_back_money') {
            $order_by .= " sa.total_back_money $orderDirection";
        } else if ($orderField == 'by_total_owe_money') {
            $order_by .= " sa.total_owe_money $orderDirection";
        } else if ($orderField == 'by_total_pay_money') {
            $order_by .= " sa.total_pay_money $orderDirection";
        } else if ($orderField == 'by_total_pay_money') {
            $order_by .= " sa.total_pay_money $orderDirection";
        } else if ($orderField == 'by_total_unpaid_money') {
            $order_by .= " sa.total_unpaid_money $orderDirection";
        } else if ($orderField == 'by_total_total_num') {
            $order_by .= " sa.total_total_num $orderDirection";
        }  else {
            $order_by .= " sa.total_num desc";
        }

        $countSql = "
				SELECT u.name,sa.* from fly_sys_user as u
				LEFT JOIN 
					(
						SELECT create_user_id,count(1) as total_num,
						sum(money) as total_money,sum(back_money) as total_back_money,sum(owe_money) as total_owe_money,sum(zero_money) as total_zero_money,
						sum(pay_money) as total_pay_money,sum(unpaid_money) as total_unpaid_money,sum(invoice_money) as total_invoice_money
						FROM pos_contract 
						WHERE $where_str
						GROUP BY create_user_id 
					) as sa
				ON sa.create_user_id=u.id
				$order_by
		";
        $totalCount = $this->C($this->cacheDir)->countRecords($countSql);    //计算记录数
        $beginRecord = ($pageNum - 1) * $pageSize;

        $sql = $countSql . " limit $beginRecord,$pageSize";
        $list = $this->C($this->cacheDir)->findAll($sql);
        $moneyRs = array('total_money' => 0, 'total_back_money' => 0, 'total_owe_money' => 0, 'total_num' => 0, 'total_pay_money' => 0, 'total_unpaid_money' => 0, 'total_zero_money' => 0, 'total_invoice_money' => 0);
        if (is_array($list)) {
            foreach ($list as $key => $row) {
                $moneyRs['total_money'] += $row['total_money'];
                $moneyRs['total_back_money'] += $row['total_back_money'];
                $moneyRs['total_owe_money'] += $row['total_owe_money'];
                $moneyRs['total_num'] += $row['total_num'];
                $moneyRs['total_pay_money'] += $row['total_pay_money'];
                $moneyRs['total_unpaid_money'] += $row['total_unpaid_money'];
                $moneyRs['total_zero_money'] += $row['total_zero_money'];
                $moneyRs['total_invoice_money'] += $row['total_invoice_money'];
            }

        }
        //$moneySql   = "select sum(money) as total_money from chart_purchase  where $where_str";
        //$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);

        $assignArray = array('list' => $list, "pageSize" => $pageSize, "totalCount" => $totalCount, "pageNum" => $pageNum, "moneyRs" => $moneyRs);
        return $assignArray;
    }

    public function chart_purchase_json()
    {
        $list = $this->chart_purchase();
        echo json_encode($list);
    }

    public function chart_purchase_show()
    {
        $list = $this->chart_purchase();
        $smarty = $this->setSmarty();
        $smarty->assign($list);
        $smarty->display('erp/chart_purchase_show.html');
    }

}//
?>