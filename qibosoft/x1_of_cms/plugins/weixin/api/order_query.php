<?php
/**
 * 订单查询-demo
 * ====================================================
 * 该接口提供所有微信支付订单的查询。
 * 当支付通知处理异常或丢失的情况，商户可以通过该接口查询订单支付状态。
 *
 */

include_once(dirname(__FILE__).'/'."lib/WxPayPubHelper.php");

//使用订单查询接口

    $orderQuery = new OrderQuery_pub();
    
    if (!$out_trade_no){
        
        return '订单号不存在！';
        
    }else{
        
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $orderQuery->setParameter("out_trade_no","$out_trade_no");//商户订单号
        //非必填参数，商户可根据实际情况选填
        //$orderQuery->setParameter("sub_mch_id","XXXX");//子商户号
        //$orderQuery->setParameter("transaction_id","XXXX");//微信订单号
        
        //获取订单查询结果
        $orderQueryResult = $orderQuery->getResult();
        
        //商户根据实际情况设置相应的处理流程,此处仅作举例
        if ($orderQueryResult["return_code"] == "FAIL") {
            return  "通信出错：".$orderQueryResult['return_msg'];
        }
        elseif($orderQueryResult["result_code"] == "FAIL")
        {

            $str = "错误代码：".$orderQueryResult['err_code']."<br>";
            $str .= "错误代码描述：".$orderQueryResult['err_code_des']."<br>";
            return $str;
            
        }elseif($orderQueryResult["result_code"] == "SUCCESS"){/*
            echo "交易状态：".$orderQueryResult['trade_state']."<br>";
            echo "设备号：".$orderQueryResult['device_info']."<br>";
            echo "用户标识：".$orderQueryResult['openid']."<br>";
            echo "是否关注公众账号：".$orderQueryResult['is_subscribe']."<br>";
            echo "交易类型：".$orderQueryResult['trade_type']."<br>";
            echo "付款银行：".$orderQueryResult['bank_type']."<br>";
            echo "总金额：".$orderQueryResult['total_fee']."<br>";
            echo "现金券金额：".$orderQueryResult['coupon_fee']."<br>";
            echo "货币种类：".$orderQueryResult['fee_type']."<br>";
            echo "微信支付订单号：".$orderQueryResult['transaction_id']."<br>";
            echo "商户订单号：".$orderQueryResult['out_trade_no']."<br>";
            echo "商家数据包：".$orderQueryResult['attach']."<br>";
            echo "支付完成时间：".$orderQueryResult['time_end']."<br>";*/
            if($orderQueryResult['trade_state']=='SUCCESS'){
                $orderQueryResult['ispay'] = true;  //付款成功的标志
                $orderQueryResult['s_orderid'] = $orderQueryResult['transaction_id'];
            }else{
                $orderQueryResult['ispay'] = false;
                $orderQueryResult['s_orderid'] = '';
            }
            return $orderQueryResult;
        }else{
            return '获取数据失败';
        }
    }

/*
 Array
 (
 [return_code] => SUCCESS
 [return_msg] => OK
 [appid] => wx4cbbd72ba92b7dc5
 [mch_id] => 1272238101
 [nonce_str] => Z4q2Kxzl7GldAjuS
 [sign] => AAA9E8FA51D574F9D273724799CD5BED
 [result_code] => FAIL
 [err_code] => ORDERNOTEXIST
 [err_code_des] => order not exist
 )
 
 
 Array
 (
 [return_code] => SUCCESS
 [return_msg] => OK
 [appid] => wx4cbbd72ba92b7dc5
 [mch_id] => 1272238101
 [nonce_str] => Wg61H0RVRO67roPo
 [sign] => 3636F876BC195A082EC524B31111402D
 [result_code] => SUCCESS
 [openid] => oCT6tuBIcwxuH4gCwBGZeKOfv1LI
 [is_subscribe] => Y
 [trade_type] => JSAPI
 [bank_type] => CFT
 [total_fee] => 100
 [fee_type] => CNY
 [transaction_id] => 4001262001201603304396117899
 [out_trade_no] => 0000008787
 [attach] => bFNXVFEQIBO|EDD0c71fbac0f
 [time_end] => 20160330130333
 [trade_state] => SUCCESS
 [cash_fee] => 100
 )
 
 
 Array
 (
 [return_code] => SUCCESS
 [return_msg] => OK
 [appid] => wx4cbbd72ba92b7dc5
 [mch_id] => 1272238101
 [nonce_str] => qzO05QZ2w8TCSeWK
 [sign] => BE5BA41F19F93FA940A27DA11121104E
 [result_code] => SUCCESS
 [openid] => oCT6tuBe0DBWvrzyVcB27QtKl5y8
 [is_subscribe] => Y
 [trade_type] => JSAPI
 [bank_type] => ICBC_DEBIT
 [total_fee] => 1
 [fee_type] => CNY
 [transaction_id] => 4002302001201603284344899395
 [out_trade_no] => 0000009470
 [attach] => bQECU1MQIBO|EDDd19cf02964
 [time_end] => 20160328130909
 [trade_state] => SUCCESS
 [cash_fee] => 1000
 [trade_state_desc] =>
 )
 */

?>