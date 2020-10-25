<?php

namespace app\common\logic;
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
 * 逻辑层模型
 */
class Vrorder
{
    /**
     * 取消订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $msg 操作备注
     * @param boolean $if_queue 是否使用队列
     * @return array
     */
    public function changeOrderStateCancel($order_info, $role, $msg, $if_queue = true)
    {

        try {

            $vrorder_model = model('vrorder');
            Db::startTrans();

            //库存、销量变更
            if ($if_queue) {
                \mall\queue\QueueClient::push('cancelOrderUpdateStorage', array($order_info['goods_id'] => $order_info['goods_num']));
            }
            else {
                \model('queue','logic')->cancelOrderUpdateStorage(array($order_info['goods_id'] => $order_info['goods_num']));
            }

            if($order_info['order_promotion_type']==2){//如果是拼团
                Db::name('ppintuangroup')->where('pintuangroup_id', $order_info['promotions_id'])->dec('pintuangroup_joined')->update();
            }
            
            $predeposit_model = model('predeposit');

            //解冻充值卡
            $rcb_amount = floatval($order_info['rcb_amount']);
            $data_rcb = array();
            $data_rcb['member_id'] = $order_info['buyer_id'];
            $data_rcb['member_name'] = $order_info['buyer_name'];
            $data_rcb['amount'] = $rcb_amount;
            $data_rcb['order_sn'] = $order_info['order_sn'];


            //解冻预存款
            $pd_amount = floatval($order_info['pd_amount']);
            $data_pd = array();
            $data_pd['member_id'] = $order_info['buyer_id'];
            $data_pd['member_name'] = $order_info['buyer_name'];
            $data_pd['amount'] = $pd_amount;
            $data_pd['order_sn'] = $order_info['order_sn'];


            if ($order_info['order_state'] == ORDER_STATE_NEW) {
                if ($rcb_amount > 0) {
                    $predeposit_model->changeRcb('order_cancel', $data_rcb);
                }
                if ($pd_amount > 0) {
                    $predeposit_model->changePd('order_cancel', $data_pd);
                }
            }
            if ($order_info['order_state'] == ORDER_STATE_PAY) {
                if ($rcb_amount > 0) {
                    $predeposit_model->changeRcb('refund', $data_rcb);
                }
                $data_pd['amount'] = $pd_amount = $order_info['order_amount'] - $rcb_amount;
                if ($pd_amount > 0) {
                    $predeposit_model->changePd('refund', $data_pd);
                }
            }
            //更新订单信息
            $update_order = array(
                'order_state' => ORDER_STATE_CANCEL, 'pd_amount' => 0, 'close_time' => TIMESTAMP, 'close_reason' => $msg
            );
            $update = $vrorder_model->editVrorder($update_order, array('order_id' => $order_info['order_id']));
            if (!$update) {
                throw new \think\Exception('保存失败', 10006);
            }

            Db::commit();
            return ds_callback(true, '更新成功');

        } catch (Exception $e) {
            Db::rollback();
            return ds_callback(false, $e->getMessage());
        }
    }

    /**
     * 支付订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $post
     * @return array
     */
    public function changeOrderStatePay($order_info, $role, $post)
    {
        try {

            $vrorder_model = model('vrorder');
            Db::startTrans();

            $predeposit_model = model('predeposit');
            //下单，支付被冻结的充值卡
            $rcb_amount = floatval($order_info['rcb_amount']);
            if ($rcb_amount > 0) {
                $data_pd = array();
                $data_pd['member_id'] = $order_info['buyer_id'];
                $data_pd['member_name'] = $order_info['buyer_name'];
                $data_pd['amount'] = $rcb_amount;
                $data_pd['order_sn'] = $order_info['order_sn'];
                $predeposit_model->changeRcb('order_comb_pay', $data_pd);
            }

            //下单，支付被冻结的预存款
            $pd_amount = floatval($order_info['pd_amount']);
            if ($pd_amount > 0) {
                $data_pd = array();
                $data_pd['member_id'] = $order_info['buyer_id'];
                $data_pd['member_name'] = $order_info['buyer_name'];
                $data_pd['amount'] = $pd_amount;
                $data_pd['order_sn'] = $order_info['order_sn'];
                $predeposit_model->changePd('order_comb_pay', $data_pd);
            }

            //更新订单状态
            $update_order = array();
            $update_order['order_state'] = ORDER_STATE_PAY;
            $update_order['payment_time'] = isset($post['payment_time']) ? strtotime($post['payment_time']) : TIMESTAMP;
            $update_order['payment_code'] = $post['payment_code'];
            $update_order['trade_no'] = $post['trade_no'];
            $update = $vrorder_model->editVrorder($update_order, array('order_id' => $order_info['order_id']));
            if (!$update) {
                throw new \think\Exception(lang('ds_common_save_fail'), 10006);
            }

            if($order_info['order_promotion_type']!=2){//虚拟商品拼团等拼团成功再发兑换码
                $this->addVrorderCode($order_info);
            }

            Db::commit();
            return ds_callback(true, '更新成功');

        } catch (Exception $e) {
            Db::rollback();
            return ds_callback(false, $e->getMessage());
        }
    }
    
    public function addVrorderCode($order_info) {
        $vrorder_model = model('vrorder');
        //发放兑换码
        $insert = $vrorder_model->addVrorderCode($order_info);
        if (!$insert) {
            throw new \think\Exception('兑换码发送失败', 10006);
        }

        // 支付成功发送买家消息
        $param = array();
        $param['code'] = 'order_payment_success';
        $param['member_id'] = $order_info['buyer_id'];
                //阿里短信参数
                $param['ali_param'] = array(
                    'order_sn' => $order_info['order_sn'],
                );
        $param['param'] = array_merge($param['ali_param'],array(
            'order_url' => (string)url('home/Membervrorder/show_order', array('order_id' => $order_info['order_id']))
        ));
        //微信模板消息
                $param['weixin_param'] = array(
                    'url' => config('ds_config.h5_site_url').'/member/vrorder_detail?order_id='.$order_info['order_id'],
                    'data'=>array(
                        "keyword1" => array(
                            "value" => $order_info['order_sn'],
                            "color" => "#333"
                        ),
                        "keyword2" => array(
                            "value" => $order_info['goods_name'],
                            "color" => "#333"
                        ),
                        "keyword3" => array(
                            "value" => $order_info['order_amount'],
                            "color" => "#333"
                        ),
                        "keyword4" => array(
                            "value" => date('Y-m-d H:i',$order_info['add_time']),
                            "color" => "#333"
                        )
                    ),
                );
        \mall\queue\QueueClient::push('sendMemberMsg', $param);

        // 支付成功发送店铺消息
        $param = array();
        $param['code'] = 'new_order';
        $param['store_id'] = $order_info['store_id'];
        $param['ali_param'] = array(
            'order_sn' => $order_info['order_sn']
        );
        $param['param'] = $param['ali_param'];
        $param['weixin_param']=array(
                    'url' => config('ds_config.h5_site_url').'/seller/vrorder_detail?order_id='.$order_info['order_id'],
                    'data'=>array(
                        "keyword1" => array(
                            "value" => $order_info['order_sn'],
                            "color" => "#333"
                        ),
                        "keyword2" => array(
                            "value" => $order_info['goods_name'],
                            "color" => "#333"
                        ),
                        "keyword3" => array(
                            "value" => $order_info['order_amount'],
                            "color" => "#333"
                        ),
                        "keyword4" => array(
                            "value" => date('Y-m-d H:i',$order_info['add_time']),
                            "color" => "#333"
                        )
                    ),
                );
        \mall\queue\QueueClient::push('sendStoremsg', $param);

        //发送兑换码到手机 
        $param = array(
            'order_id' => $order_info['order_id'], 'buyer_id' => $order_info['buyer_id'],
            'buyer_phone' => $order_info['buyer_phone']
        );
        \mall\queue\QueueClient::push('sendVrCode', $param);
    }

    /**
     * 完成订单
     * @param int $order_id
     * @return array
     */
    public function changeOrderStateSuccess($order_id)
    {
        $vrorder_model = model('vrorder');
        $condition = array();
        $condition[] = array('vr_state','=',0);
        $condition[] = array('refund_lock','in', array(0, 1));
        $condition[] = array('order_id','=',$order_id);
        $condition[] = array('vr_indate','>',TIMESTAMP);
        $order_code_info = $vrorder_model->getVrordercodeInfo($condition, '*');
        if (empty($order_code_info)) {
            $update = $vrorder_model->editVrorder(array(
                                                     'order_state' => ORDER_STATE_SUCCESS, 'finnshed_time' => TIMESTAMP
                                                 ), array('order_id' => $order_id));
            if (!$update) {
                ds_callback(false, '更新失败');
            }
        }

            
        $order_info = $vrorder_model->getVrorderInfo(array('order_id' => $order_id));
        //添加会员积分
        if (config('ds_config.points_isuse') == 1) {
            model('points')->savePointslog('order', array(
                'pl_memberid' => $order_info['buyer_id'], 'pl_membername' => $order_info['buyer_name'],
                'orderprice' => $order_info['order_amount'], 'order_sn' => $order_info['order_sn'],
                'order_id' => $order_info['order_id']
            ), true);
        }

        //添加会员经验值
        model('exppoints')->saveExppointslog('order', array(
            'explog_memberid' => $order_info['buyer_id'], 'explog_membername' => $order_info['buyer_name'],
            'orderprice' => $order_info['order_amount'], 'order_sn' => $order_info['order_sn'],
            'order_id' => $order_info['order_id']
        ), true);

        return ds_callback(true, '更新成功');
    }
}