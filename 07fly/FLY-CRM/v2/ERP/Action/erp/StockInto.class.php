<?php

/*
 *
 * erp.StockStockIntoSku  入库单
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

class StockInto extends Action
{
    private $cacheDir = '';//缓存目录
    private $type = '';//缓存目录
    private $member = '';//缓存目录
    private $shop = '';//缓存目录

    public function __construct()
    {
        $this->auth = _instance('Action/sysmanage/Auth');
        $this->sys_user = _instance('Action/sysmanage/User');
        $this->store = _instance('Action/erp/StockStore');
        $this->contract = _instance('Action/erp/PosContract');
        $this->contract_list = _instance('Action/erp/PosContractList');
        $this->stock_goods_sku = _instance('Action/erp/StockGoodsSku');
    }

    //库存清单商品SKU
    public function stock_into()
    {
        //**获得传送来的数据作分页处理
        $pageNum = $this->_REQUEST("pageNum");//第几页
        $pageSize = $this->_REQUEST("pageSize");//每页多少条
        $pageNum = empty($pageNum) ? 1 : $pageNum;
        $pageSize = empty($pageSize) ? $GLOBALS["pageSize"] : $pageSize;

        $category_id = $this->_REQUEST("category_id");
        $goods_name = $this->_REQUEST("goods_name");
        $sku_name = $this->_REQUEST("sku_name");
        $code = $this->_REQUEST("code");
        $where_str = " s.into_id>0";

        //获得分类及子类的商品条件
        if (!empty($category_id)) {
            $child_arr = $this->goods_category->goods_category_all_child($category_id);
            if (empty($child_arr)) {
                $child_txt = "$category_id";
            } else {
                $child_txt = implode(',', $child_arr) . ",$category_id";
            }
            $where_str .= " and g.category_id in ($child_txt)";
        }
        if (!empty($goods_name)) {
            $where_str .= " and g.goods_name like '%$goods_name%'";
        }
        if (!empty($sku_name)) {
            $where_str .= " and s.sku_name like '%$sku_name%'";
        }
        if (!empty($code)) {
            $where_str .= " and s.code like '%$code%'";
        }
        //排序操作
        $orderField = $this->_REQUEST("orderField");
        $orderDirection = $this->_REQUEST("orderDirection");
        $order_by = "order by";
        if ($orderField == 'by_saleprice') {
            $order_by .= " s.sale_price $orderDirection";
        } else if ($orderField == 'by_marketprice') {
            $order_by .= " s.market_price $orderDirection";
        } else if ($orderField == 'by_costprice') {
            $order_by .= " s.cost_price $orderDirection";
        } else if ($orderField == 'by_stock') {
            $order_by .= " s.stock $orderDirection";
        } else {
            $order_by .= " s.into_id desc";
        }
        $countSql = "select * from stock_into as s where $where_str";
        $totalCount = $this->C($this->cacheDir)->countRecords($countSql);
        $beginRecord = ($pageNum - 1) * $pageSize;//计算开始行数
        $sql = "select s.* from stock_into as s where $where_str $order_by limit $beginRecord,$pageSize";
        $list = $this->C($this->cacheDir)->findAll($sql);
        foreach ($list as $key => $row) {
            $list[$key]['create_user_arr'] = $this->sys_user->user_get_one($row['create_user_id']);
            $list[$key]['into_user_arr'] = $this->sys_user->user_get_one($row['into_user_id']);
            $list[$key]['status_arr'] = $this->stock_into_status($row['status']);
            $list[$key]['store_arr'] = $this->store->stock_store_get_one($row['store_id']);
            //$list[$key]['goods_category']	=$this->goods_category->goods_category_get_one($row['category_id']);
        }
        $assignArray = array('list' => $list, "pageSize" => $pageSize, "totalCount" => $totalCount, "pageNum" => $pageNum);
        return $assignArray;
    }

    public function stock_into_json()
    {
        $assArr = $this->stock_into();
        echo json_encode($assArr);
    }

    public function stock_into_show()
    {
        $assArr = $this->stock_into();
        $smarty = $this->setSmarty();
        $smarty->assign($assArr);
        $smarty->display('erp/stock_into_show.html');
    }

    //从采购合同单处理直接生成入库单
    public function stock_into_contract_add()
    {

        $title = $this->_REQUEST("title");
        $contract_id = $this->_REQUEST("contract_id");
        $store_id = $this->_REQUEST("store_id");
        $contract_list_id = $this->_REQUEST("list_id");
        $sku_id = $this->_REQUEST("sku_id");
        $sku_name = $this->_REQUEST("sku_name");
        $goods_id = $this->_REQUEST("goods_id");
        $goods_name = $this->_REQUEST("goods_name");
        $cost_price = $this->_REQUEST("cost_price");
        $num = $this->_REQUEST("num");
        $into_num = $this->_REQUEST("into_num");
        $owe_num = $this->_REQUEST("owe_num");
        $owe_money = $this->_REQUEST("owe_money");
        $total_number = array_sum($owe_num);
        $total_money = array_sum($owe_money);

        if ($total_number <= 0) {
            $this->L("Common")->ajax_json_error('本次入库数量合计不能小于0');
            return false;
        }

        foreach ($sku_id as $ik => $sku_one_id) {
            $t_num = $into_num[$ik] + $owe_num[$ik];
            if ($t_num > $num[$ik]) {
                $this->L("Common")->ajax_json_error('本次入库数量不能大于采购数据');
                return false;
            }
        }

        //第一步生成入库单
        $into_data = array(
            'title' => $title,
            'store_id' => $store_id,
            'contract_id' => $contract_id,
            'money' => $total_money,
            'number' => $total_number,
            'into_type' => '采购入库',
            'create_time' => NOWTIME,
            'create_user_id' => SYS_USER_ID,
        );
        //插入记录，生成入库单
        $into_id = $this->C($this->cacheDir)->insert('stock_into', $into_data);
        if ($into_id > 0) {
            //生成成入库明细，
            foreach ($sku_id as $i => $sku_one_id) {
                //判断入库数据大于0166601275
                if ($owe_num[$i] > 0 && $owe_money[$i] > 0) {
                    $into_data = array(
                        'into_id' => $into_id,
                        'store_id' => $store_id,
                        'contract_id' => $contract_id,
                        'contract_list_id' => $contract_list_id[$i],
                        'sku_id' => $sku_id[$i],
                        'sku_name' => $sku_name[$i],
                        'goods_id' => $goods_id[$i],
                        'goods_name' => $goods_name[$i],
                        'price' => $cost_price[$i],
                        'number' => $owe_num[$i],
                        'money' => $owe_money[$i],
                        'create_time' => NOWTIME,
                        'create_user_id' => SYS_USER_ID,
                    );
                    $this->C($this->cacheDir)->insert('stock_into_list', $into_data);
                }
            }
            //修改采购单为待入库
            $this->C($this->cacheDir)->modify('pos_contract', array('rece_status' => '3'), "contract_id='$contract_id'");
            //更新合同执行状态
            $this->contract->pos_contract_modify_status($contract_id);
            $this->L("Common")->ajax_json_success("入库单生成成功");

        } else {
            $this->L("Common")->ajax_json_error('入库单生成失败');
            return false;
        }
    }

    //确认入库
    public function stock_into_sure()
    {
        $into_id = $this->_REQUEST("into_id");
        $into_data = array(
            'status' => '1',
            'into_time' => NOWTIME,
            'into_user_id' => SYS_USER_ID,
        );
        $this->C($this->cacheDir)->modify('stock_into', $into_data, "into_id='$into_id'");

        //更新库存清单，不存在就添加，以仓库，产品，SKU库存，的编号为标识
        $sql = "select * from stock_into_list where into_id='$into_id'";
        $list = $this->C($this->cacheDir)->findAll($sql);
        foreach ($list as $key => $row) {
            $contract_id = $row['contract_id'];
            $contract_list_id = $row['contract_list_id'];
            //更改采购订单的数据
            $this->contract_list->pos_contract_list_stock_into_sure($contract_list_id, $row['number'], $row['money']);
            //更改库存清单 的数据
            $this->stock_goods_sku->stock_goods_sku_into_sure($row);
        }

        //更新入库清单记录的入库人员
        $into_list_data = array('into_time' => NOWTIME, 'into_user_id' => SYS_USER_ID,);
        $this->C($this->cacheDir)->modify('stock_into_list', $into_list_data, "into_id='$into_id'");

        //修改采购单入库状态
        $this->contract->pos_contract_modify_rece_status($contract_id);
        $this->L("Common")->ajax_json_success("入库成功");
    }

    //删除入库
    public function stock_into_del()
    {
        $into_id = $this->_REQUEST("into_id");
        $into_arr = explode(',', $into_id);
        foreach ($into_arr as $one_id) {
            $into_sql = "select * from stock_into where into_id='$one_id'";
            $into_one = $this->C($this->cacheDir)->findOne($into_sql);
            $contract_id = $into_one['contract_id'];

            //第一步、当已经入库的数据需要撤消采购单明细的数据
            if ($into_one['status'] == '1') {
                $into_list_sql = "select * from stock_into_list where into_id='$one_id'";
                $into_list_list = $this->C($this->cacheDir)->findAll($into_list_sql);
                foreach ($into_list_list as $row) {
                    $contract_id = $row['contract_id'];
                    $contract_list_id = $row['contract_list_id'];
                    //更改采购订单明细的数据
                    $this->contract_list->pos_contract_list_stock_into_del($contract_list_id, $row['number'], $row['money']);
                    //更改库存清单的数据
                    $this->stock_goods_sku->stock_goods_sku_into_del($row['sku_id'], $row['goods_id'], $row['store_id'], $row['number'], $row['money']);
                }
            }
            //第二步、修改采购单收货库状态
            $this->contract->pos_contract_modify_rece_status($contract_id);

            //第三步、删除入库单和明细
            $this->C($this->cacheDir)->delete('stock_into', "into_id='$one_id'");
            $this->C($this->cacheDir)->delete('stock_into_list', "into_id='$one_id'");
            $this->L("Common")->ajax_json_success("删除成功");
        }
    }

    //入库状态
    public function stock_into_status($key = null)
    {
        $data = array(
            "-1" => array(
                'status_name' => '未入库',
                'status_name_html' => '<span class="label label-warning">未入库<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'stock_sure',
                        'color' => '#7266BA',
                        'name' => '确认入库'
                    ),
                    '1' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    ),
                ),
            ),
            "1" => array(
                'status_name' => '已入库',
                'status_name_html' => '<span class="label label-info">已入库<span>',
                'status_operation' => array(
                    '0' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    )
                ),
            )
        );
        return ($key) ? $data[$key] : $data;
    }
}//
?>