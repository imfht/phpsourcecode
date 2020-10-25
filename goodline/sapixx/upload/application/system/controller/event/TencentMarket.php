<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 腾讯云市场
 */
namespace app\system\controller\event;
use think\Controller;
use think\helper\Time;
use app\common\model\SystemApis;
use app\common\model\SystemWeb;
use app\common\model\SystemMember;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberMiniappOrder;
use app\common\model\SystemMiniapp;
use app\common\model\SystemMemberCloud;
use app\common\model\SystemMemberMiniappCloud;
use app\common\model\SystemMemberBankBill;
use app\common\model\SystemMemberCloudProduct;
use think\facade\Log;

class TencentMarket extends Controller{

    public function auth(){
        $param = $this->request->param();
        if(empty($param['accountId']) || empty($param['action'])){
            return json(['success' => "false",'msg' => 'accountId or action not is null']);
        }
        switch ($param['action']){
            case 'verifyInterface': //身份校验接口
                if(!$param['signature'] || !$param['timestamp'] || !$param['eventId']){
                    return json(['success' => "false",'msg' => 'signature not is null']);
                }
                $config = SystemApis::config('wechatcloud');
                if(empty($config)){
                    return json(['success' => "false",'msg' => 'config not is null']);
                }
                $token = $config['token'];
                $check = self::checkSignature($param['signature'], $token, $param['timestamp'], $param['eventId']);//这里使用的是腾讯云提供的签名校验函数
                if (!$check) {
                    return json(['success' => "false",'msg' => 'Verification failed']);
                } else {
                    return json(['echoback' => $param['echoback']]);
                }
                break;
            case 'createInstance': //购买应用
                $memberCloudProduct = SystemMemberCloudProduct::where(['product_id' => $param['productId']])->find();
                if(empty($memberCloudProduct)){
                    return json(['success' => "false"]);
                }
                $miniapp_id  = $memberCloudProduct->miniapp_id;
                $orderId     = $param['orderId'];
                $accountId   = $param['accountId'];
                //$requestId   = $param['requestId'];
                $openId      = $param['openId'];
                $productInfo = $param['productInfo'];
                //创建云市场用户
                $info = SystemMemberCloud::where(['openId' => $openId])->find();
                if(empty($info)){
                    $data['username']      = '腾讯云'.getcode(5);
                    $data['password']      = password_hash(md5($openId),PASSWORD_DEFAULT);
                    $data['safe_password'] = password_hash(md5('123456'),PASSWORD_DEFAULT); 
                    $data['login_time']    = time();
                    $data['update_time']   = time();
                    $data['create_time']   = time();
                    $data['login_ip']      = request()->ip();
                    $last_id =  SystemMember::insertGetId($data);
                    SystemMemberCloud::create(['member_id'=>$last_id,'openId' => $openId,'create_time'=> time()]);
                }else{
                    $last_id = $info->member_id;
                }
                $miniapp  = SystemMiniapp::where(['id' => $miniapp_id,'is_lock' => 0])->field('id,sell_price,template_id,title')->find();
                if(empty($miniapp)){
                    return json(['success' => "false",'msg' => 'product is miss']);
                }
                //新增购买列表
                $order['member_id']  = $last_id;
                $order['miniapp_id'] = $miniapp_id;
                $order['update_var'] = (int)$miniapp->template_id;
                $order['start_time'] = time();
                if($productInfo['isTrial'] == 'true'){ 
                    $order['end_time'] = Time::daysAfter(7);  //试用版
                }else{
                    $order['end_time'] = self::formatDate($productInfo['timeUnit'],$productInfo['timeSpan']); //正式版
                }
                $member_order_id   = SystemMemberMiniappOrder::insertGetId($order);
                $member_miniapp_id = SystemMemberMiniapp::insertGetId([
                    'miniapp_order_id' => $member_order_id,
                    'member_id'        => $last_id,
                    'miniapp_id'       => $miniapp_id,
                    'appname'          => $miniapp->title,
                    'create_time'      => time()
                ]);
                SystemMemberMiniapp::where(['id' => $member_miniapp_id])->update(['service_id' => uuid(3,true,$member_miniapp_id)]); //更新服务ID
                self::createBill($last_id,$miniapp);
                //同时我们需要向腾讯云返回一个免登后台地址，在这里我选用signid作为免登的token
                $signid = create_code(time()); 
                //创建云产品
                SystemMemberMiniappCloud::create([
                    'member_id'         => $last_id,
                    'signId'            => $signid,
                    'orderId'           => $orderId,
                    'accountId'         => $accountId,
                    'openId'            => $openId,
                    'productId'         => $miniapp_id,
                   // 'requestId'         => $requestId,
                    'productInfo'       => json_encode($productInfo),
                    'create_time'       => time(),
                    'member_order_id'   => $member_order_id,
                    'member_miniapp_id' => $member_miniapp_id
                ]);
                //这里构造个腾讯云接口规定的返回参数，返回
                $SystemWeb = SystemWeb::config();
                $post_data = [
                    'signId'=> $signid,//返回给腾讯云实例标识，之后的接口腾讯云会带着这个参数
                    'appInfo'=>[
                        'website'=> 'https://'.$SystemWeb->url, //网站地址
                        'authUrl'=> url('system/passport.login/cloud',['token' => $signid],'html',true) //后台免登地址
                    ],
                ];
                return json($post_data);
                break;
            case 'renewInstance': //实例续费通知接口
                $signid = $param['signId'];
                $instanceExpireTime = $param['instanceExpireTime'];//这个参数是datetime格式的实例到期日期，我们通过此参数修改数据库内的商家到期时间
                $userCloud = SystemMemberMiniappCloud::where(['signId' => $signid])->find();
                if($userCloud){
                    SystemMemberMiniappOrder::where(['id' => $userCloud->member_order_id])->update(['end_time' => strtotime($instanceExpireTime)]);
                    self::createBill($userCloud->member_id,SystemMiniapp::where(['id' => $userCloud->productId])->find());
                    return json(['success' => "true"]);
                }else{
                    return json(['success' => "false"]);
                }
                break;
            case 'modifyInstance':  //实例配置变更通知接口 这个接口是在商家试用转正式版支付完成时调用的
                $signid = $param['signId'];
                $instanceExpireTime = $param['instanceExpireTime'];
                $userCloud = SystemMemberMiniappCloud::where(['signId' => $signid])->find();
                if($userCloud){
                    SystemMemberMiniappOrder::where(['id' => $userCloud->member_order_id])->update(['end_time' => strtotime($instanceExpireTime)]);
                    //创建帐单
                    self::createBill($userCloud->member_id,SystemMiniapp::where(['id' => $userCloud->productId])->find());
                    return json(['success' => "true"]);
                }else{
                    return json(['success' => "false"]);
                }
                break;
            case 'expireInstance':  //实例过期通知接口
                return json(['success' => "true"]);
                break;
            case 'destroyInstance':  //实例销毁通知接口
                $signid = $param['signId'];
                $userCloud = SystemMemberMiniappCloud::where(['signId' => $signid])->find();
                if($userCloud){
                    SystemMemberMiniapp::where(['id' => $userCloud->member_miniapp_id])->update(['is_lock' => 1]);
                    return json(['success' => "true"]);
                }else{
                    return json(['success' => "false"]);
                }
                break;
        }
    }

    /**
     * 腾讯云签名校验
     * @param $signature
     * @param $token
     * @param $timestamp
     * @param $eventId
     * @return bool
     */
    protected function checkSignature($signature, $token, $timestamp, $eventId){
        $currentTimestamp = time();
        if ($currentTimestamp - $timestamp > 30) {
            return false;
        }
        $timestamp = (string)$timestamp;
        $eventId   = (string)$eventId;
        $params    = array($token, $timestamp, $eventId);
        sort($params, SORT_STRING);
        $str              = implode('', $params);
        $requestSignature = hash('sha256', $str);
        return $signature === $requestSignature;
    }

    /**
     * @param $timeUnit
     * @param $timeSpan
     * @return false|string
     * 格式化时间
     */
    protected function formatDate($timeUnit,$timeSpan){
        $maturity_date = 0;
        switch ($timeUnit){
            case 'y':
                //购买年
                $maturity_date = strtotime("+".$timeSpan." year");
                break;
            case 'm':
                //购买月
                $maturity_date = strtotime("+".$timeSpan." month");
                break;
            case 'd':
                //购买日
                $maturity_date = strtotime("+".$timeSpan." day");
                break;
        }
        return $maturity_date;
    }
    
    /**
     * 创建账单
     * @param [type] $last_id
     * @param [type] $miniapp
     * @return void
     */
    protected function  createBill($last_id,$miniapp){
        $billData = [
            'state'       => 1,
            'money'       => 0,
            'member_id'   => $last_id,
            'message'     => '通过云市场购买应用程序' . $miniapp->title,
            'update_time' => time(),
        ];
        SystemMemberBankBill::create($billData);
    }
}