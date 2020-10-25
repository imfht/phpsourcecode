<?php

namespace app\common\model;


use think\facade\Db;
/**
 * ============================================================================
 * DSMall多用户商城
 * ============================================================================
 * 版权所有 2014-2028 长沙德尚网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.csdeshang.com
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 数据层模型
 */
class Vrrefund extends BaseModel {
    public $page_info;
   
    /**
     * 增加退款
     * @access public
     * @author csdeshang
     * @param type $refund_array 退款数组数据
     * @param type $order 排序
     * @return boolean
     */
    public function addVrrefund($refund_array, $order = array()) {
        if (!empty($order) && is_array($order)) {
            $refund_array['order_id'] = $order['order_id'];
            $refund_array['order_sn'] = $order['order_sn'];
            $refund_array['store_id'] = $order['store_id'];
            $refund_array['store_name'] = $order['store_name'];
            $refund_array['buyer_id'] = $order['buyer_id'];
            $refund_array['buyer_name'] = $order['buyer_name'];
            $refund_array['goods_id'] = $order['goods_id'];
            $refund_array['goods_name'] = $order['goods_name'];
            $refund_array['goods_image'] = $order['goods_image'];
            $refund_array['commis_rate'] = $order['commis_rate'];
        }
        $refund_array['refund_sn'] = $this->getVrrefundSn($refund_array['store_id']);

        try {
            Db::startTrans();
            $refund_id = Db::name('vrrefund')->insertGetId($refund_array);
            $code_array = explode(',', $refund_array['redeemcode_sn']);
            $vrorder_model = model('vrorder');
            $vrorder_model->editVrorderCode(array('refund_lock' => 1), array(array('vr_code','in', $code_array))); //退款锁定
            Db::commit();
            return $refund_id;
        } catch (Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 平台退款处理
     * @access public
     * @author csdeshang
     * @param type $refund 退款
     * @return boolean
     */
    public function editVrorderRefund($refund) {
        $refund_id = $refund['refund_id'];
        $refund_lock = '0'; //退款锁定状态:0为正常,1为锁定,2为同意
        $vrorder_model = model('vrorder');
        
        $order_id = $refund['order_id']; //订单编号
        $order = $vrorder_model->getVrorderInfo(array('order_id' => $order_id));

        //生成out_request_no 支付宝部分退款必传唯一的标识一次退款请求号
        $order['out_request_no'] = $order['order_sn'];

        //订单总金额，单次支付包含多个店铺订单的总金额（针对于微信退款原路返回需要传订单总金额）
        $order['total_order_amount'] = 0;
        $order_array = $vrorder_model->getVrOrderList(array('trade_no' => $order['trade_no']));
        foreach ($order_array as $key => $value){
            $order['total_order_amount'] += $vrorder_model->getVrorderInfo(array('order_id' => $value['order_id']), 'order_amount')['order_amount'];
        }

        $order_amount = $order['order_amount']; //订单金额
        $rcb_amount = $order['rcb_amount']; //充值卡支付金额
        $pd_amount = $order['pd_amount']; //预存款支付金额
        $predeposit_amount = $order_amount - $order['refund_amount'] - $rcb_amount; //可退预存款金额
        
        
        $not_trade_refund = TRUE; //在线支付 不原路返还
        $alipay_payment_list = array('alipay', 'alipay_app', 'alipay_h5');
        $wxpay_payment_list = array('wxpay_app', 'wxpay_h5', 'wxpay_jsapi', 'wxpay_minipro', 'wxpay_native');

        //未使用预存款支付 以及  充值卡支付的订单 才支持订单原路返还。
        if ($refund['admin_state'] == '2' && $predeposit_amount > 0 && (in_array($order['payment_code'], $alipay_payment_list) || in_array($order['payment_code'], $wxpay_payment_list)) && $rcb_amount == 0 && $pd_amount == 0) {
            if (in_array($order['payment_code'], $alipay_payment_list)) {
                $payment_code = 'alipay';
            }
            if (in_array($order['payment_code'], $wxpay_payment_list)) {
                $payment_code = 'wxpay_native';
            }
            //调用支付接口处理原路退款
            $logic_payment = model('payment', 'logic');
            $result = $logic_payment->getPaymentInfo($payment_code);
            if (!$result['code']) {
                return FALSE;
            }
            $payment_info = $result['data'];

            //支付宝/微信 未开启原路返回
            if (($payment_code == 'alipay' && $payment_info['payment_config']['alipay_trade_refund_state'] == 1) || ($payment_code == 'wxpay_native' && $payment_info['payment_config']['wx_trade_refund_state'] == 1)) {
                //原路返还金额
                $trade_refund_amount = $refund['refund_amount']; //退预存款金额
                if ($refund['refund_amount'] > $predeposit_amount) {
                    $trade_refund_amount = $predeposit_amount;
                }

                $payment_api = new $payment_code($payment_info);
                $result = $payment_api->trade_refund($order, $trade_refund_amount);
                if (!$result['code']) {
                    return FALSE;
                }
                $not_trade_refund = FALSE;
            }
        }



        try {
            Db::startTrans();
            $state = $this->editVrrefund(array('refund_id' => $refund_id), $refund); ////更新退款
            if ($state && $refund['admin_state'] == '2') {//审核状态:1为待审核,2为同意,3为不同意
                $refund_lock = '2';

                $predeposit_model = model('predeposit');
                if (($rcb_amount > 0) && ($refund['refund_amount'] > $predeposit_amount)  && $not_trade_refund) {//退充值卡
                    $log_array = array();
                    $log_array['member_id'] = $order['buyer_id'];
                    $log_array['member_name'] = $order['buyer_name'];
                    $log_array['order_sn'] = $order['order_sn'];
                    $log_array['amount'] = $refund['refund_amount'];
                    if ($predeposit_amount > 0) {
                        $log_array['amount'] = $refund['refund_amount'] - $predeposit_amount;
                    }
                    $state = $predeposit_model->changeRcb('refund', $log_array); //增加买家可用充值卡金额
                }
                if ($predeposit_amount > 0  && $not_trade_refund) {//退预存款
                    $log_array = array();
                    $log_array['member_id'] = $order['buyer_id'];
                    $log_array['member_name'] = $order['buyer_name'];
                    $log_array['order_sn'] = $order['order_sn'];
                    $log_array['amount'] = $refund['refund_amount']; //退预存款金额
                    if ($refund['refund_amount'] > $predeposit_amount) {
                        $log_array['amount'] = $predeposit_amount;
                    }
                    $state = $predeposit_model->changePd('refund', $log_array); //增加买家可用预存款金额
                }
                if ($state) {
                    $order_array = array();
                    $order_amount = $order['order_amount']; //订单金额
                    $refund_amount = $order['refund_amount'] + $refund['refund_amount']; //退款金额
                    $order_array['refund_state'] = ($order_amount - $refund_amount) > 0 ? 1 : 2;
                    $order_array['refund_amount'] = ds_price_format($refund_amount);
                    $state = $vrorder_model->editVrorder($order_array, array('order_id' => $order_id)); //更新订单退款
                }
            }
            if ($state) {
                $code_array = explode(',', $refund['redeemcode_sn']);
                $state = $vrorder_model->editVrorderCode(array('refund_lock' => $refund_lock), array(array('vr_code','in', $code_array))); //更新退款的兑换码
                if ($state && $refund['admin_state'] == '2') {
                    model('vrorder','logic')->changeOrderStateSuccess($order_id); //更新订单状态
                }
            }
            Db::commit();
            return $state;
        } catch (Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 修改退款
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $data 数据
     * @return boolean
     */
    public function editVrrefund($condition, $data) {
        if (empty($condition)) {
            return false;
        }
        if (is_array($data)) {
            $result = Db::name('vrrefund')->where($condition)->update($data);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 退款编号
     * @access public
     * @author csdeshang
     * @param type $store_id 店铺ID
     * @return string
     */
    public function getVrrefundSn($store_id) {
        $result = mt_rand(100, 999) . substr(500 + $store_id, -3) . date('ymdHis');
        return $result;
    }

    /**
     * 退款记录
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @param type $pagesize 分页
     * @param type $limit 限制
     * @param type $fields 字段
     * @return type
     */

        public function getVrrefundList($condition = array(), $pagesize = '',  $field = '*', $order = 'refund_id desc', $limit = 0) {
        if($pagesize){
            $result = Db::name('vrrefund')->field($field)->where($condition)->order($order)->paginate(['list_rows'=>$pagesize,'query' => request()->param()],false);
            $this->page_info = $result;
            $result = $result->items();
        }else{
            $result = Db::name('vrrefund')->field($field)->where($condition)->order($order)->limit($limit)->select()->toArray();
        }
        
        
        return $result;
    }

    /**
     * 取得退款记录的数量
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function getVrrefundCount($condition) {
        $result = Db::name('vrrefund')->where($condition)->count();
        return $result;
    }

    /**
     * 详细页右侧订单信息
     * @access public
     * @author csdeshang
     * @param type $order_condition 条件
     * @return type
     */
    public function getRightVrorderList($order_condition,$order_id) {
        $vrorder_model = model('vrorder');
        $order_info = $vrorder_model->getVrorderInfo($order_condition);

        $order_list = array();
        $order_list[$order_id] = $order_info;
        $order_list = $vrorder_model->getCodeRefundList($order_list); //没有使用的兑换码列表
        $order_info = $order_list[$order_id];
        $store_model = model('store');
        $store = $store_model->getStoreInfo(array('store_id' => $order_info['store_id']));

        //显示退款
        $order_info['if_refund'] = $vrorder_model->getVrorderOperateState('refund', $order_info);

        $code_list=array();
        if ($order_info['if_refund']) {
            $code_list = $order_info['code_list'];
        }
        return array('order_info'=>$order_info,'store'=>$store,'code_list'=>$code_list);
    }

    /**
     * 获得退款的店铺列表
     * @access public
     * @author csdeshang
     * @param type $list 列表
     * @return type
     */
    public function getVrrefundStoreList($list) {
        $store_ids = array();
        if (!empty($list) && is_array($list)) {
            foreach ($list as $key => $value) {
                $store_ids[] = $value['store_id']; //店铺编号
            }
        }
        $field = 'store_id,store_name,member_id,member_name,seller_name,store_company_name,store_qq,store_ww,store_phone';
        return model('store')->getStoreMemberIDList($store_ids, $field);
    }
 
    /**
     * 获取一条退款记录
     * @access public
     * @author csdeshang
     * @param type $condition 条件
     * @return type
     */
    public function getOneVrrefund($condition){
        $refund = Db::name('vrrefund')->where($condition)->find();
        return $refund;
    }

    /**
     * 向模板页面输出退款状态
     * @access public
     * @author csdeshang
     * @param type $type 类型
     * @return string
     */
    public function getRefundStateArray($type = 'all') {
        $admin_array = array(
            '1' => '待审核',
            '2' => '同意',
            '3' => '不同意'
        ); //退款状态:1为待审核,2为同意,3为不同意

        $state_data = array(
            'admin' => $admin_array
        );
        if ($type == 'all')
            return $state_data; //返回所有
        return $state_data[$type];
    }

}
