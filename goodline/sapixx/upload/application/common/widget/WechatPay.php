<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 微信支付统一处理控制器
 */
namespace app\common\widget;
use EasyWeChat\Factory;  //微信公众号
use app\common\model\ConfigApis;
use app\common\model\MemberMiniapp;
use app\common\model\MemberPayment;
use Exception;

class WechatPay{

    /**
     * 微信传统统一接口
     * 以是否传入sub_mchid商户号作为判断是否使用的是服务商模式
     * @param integer $app_id      //所属在平台开通的应用ID
     * @param boolean $is_official //是否公众号
     * @param string  $sub_mchid   //子商户号(是否服务商模式)
     * @return void
     */
    public function doPay(int $app_id = 0,bool $official = false,string $sub_mchid = NULL){
        try{
            if ($app_id) {
                $config = MemberPayment::config($app_id,'wepay');
                if(empty($config)){
                    return ['code'=>0,'msg'=>'请确认微信支付配置是否有问题'];
                }
                $app = MemberMiniapp::field('miniapp_appid,mp_appid,is_psp,psp_appid')->where(['id' => $app_id])->find();
                if(empty($official)){ //小程序
                    if(empty($app->miniapp_appid)){
                        return ['code'=>0,'msg'=>'未开通小程序支付'];
                    }
                    $config['app_id'] = $app->miniapp_appid;
                }else{//公众号
                    if(empty($app->mp_appid)){
                        return ['code'=>0,'msg'=>'未开通公众号支付'];
                    }
                    $config['app_id'] = $app->mp_appid;
                }
                if(empty($sub_mchid)){
                    return Factory::payment($config);
                }else{
                    if($app->is_psp && empty($app->psp_appid)){
                        return ['code'=>0,'msg'=>'未配置服务商APPID'];
                    }

                    //服务商模式
                    $config['app_id'] = $app->psp_appid;
                    $appid = empty($official) ? $app->miniapp_appid : $app->mp_appid; //判断发起支付的是小程序还是公众号
                    return Factory::payment($config)->setSubMerchant($sub_mchid,$appid);
                }
            }else{
                return Factory::payment(ConfigApis::config('wepay'));
            }
        }catch (Exception $e) {
            return ['code'=>0,'msg' => $e->getMessage()];
        }
    }
        
    /**
     * 微信支付统一下单参数
     * $data = [
     *    'mchid'      //商户号（空是正常支付,不为空是微信服务商模式支付）（非必填）
     *    'miniapp_id' //付款来自应用 ID（必填）
     *    'name'       //产品名称（必填）
     *    'order_no'   //单号（必填）
     *    'total_fee'  //金额（分）（必填）
     *    'openid'     //付款ID（必填）
     *    'note'       //备注（非必填）
     *    'notify_url' //回调地址（必填）
     * ]   
     * @param array   $data      //统一下单参数
     * @param integer $app      //所属在平台开通的应用ID
     * @param boolean $official //是否公众号
     */
    public function orderPay(array $data,bool $official = false){
        try{
            $order = [
                'trade_type'   => empty($data['trade_type']) ? 'JSAPI' : $data['trade_type'],
                'body'         => $data['name'],
                'out_trade_no' => (string)$data['order_no'],
                'total_fee'    => $data['total_fee'],   //分
                'notify_url'   => $data['notify_url']
            ];
            if(isset($data['note'])){
                $order['attach'] = $data['note'];
            }
            //服务商模式
            $sub_mchid = NULL;
            if(isset($data['mchid'])){
                $sub_mchid           = $data['mchid'];
                //JSAPI模式下Openid必传
                if(empty($data['trade_type'])){
                    $order['sub_openid'] = $data['openid'];
                }
                if(isset($data['profit_sharing'])){  //是否有分账参数
                    $order['profit_sharing'] = $data['profit_sharing'];
                }
            }else{
                if(empty($data['trade_type'])) {
                    $order['openid'] = $data['openid'];
                }
            }
            $wechat = self::doPay($data['miniapp_id'],$official,$sub_mchid);
            $result = $wechat->order->unify($order);
            if($result['return_code'] == 'SUCCESS'){
                if($result['result_code'] == 'SUCCESS'){
                    if(empty($data['trade_type'])){
                        //JSAPI模式
                        return ['code'=>200,'msg'=>'成功','data' => $wechat->jssdk->sdkConfig($result['prepay_id'])];
                    }else{
                        //NATIVE模式
                        return ['code'=>200,'msg'=>'成功','data' => $result];
                    }
                }else{
                    return ['code'=>0,'msg'=> $result['err_code_des']];
                }
            }else{
                return ['code'=>0,'msg'=> $result['return_msg']];
            }
        }catch (Exception $e) {
            return ['code'=>0,'msg'=> $e->getMessage()];
        }
    }
}