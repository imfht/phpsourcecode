<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\mpbase\controller;

use think\Controller;
use com\TPWechat;
use com\Wxpay\lib\WxPayConfig;
use com\Wxpay\lib\WxPayOrderQuery;
use com\Wxpay\lib\WxPayApi;
use think\Request;
/**
 * 微信交互控制器，中控服务器
 * 主要获取和反馈微信平台的数据，分析用户交互和系统消息分发。
 */
class Weixin extends Controller {

    protected $options = array(     //实例化wechat SDK的参数
          'token'=>APP_TOKEN, //填写你设定的key
          'encodingaeskey'=>'', //填写加密用的EncodingAESKey
          'appid'=>'', //填写高级调用功能的app id
          'appsecret'=>'', //填写高级调用功能的密钥
          'debug'=>'' //调试状态
      );

    protected $member_public;   //数据库中保存的公众号信息
    protected $weObj;          //自动注入的wechat SDK实例

    //TP5 的架构方法绑定（属性注入）的对象
    public function __construct(TPWechat $weObj)
    {
        $this->weObj = $weObj;

        parent::__construct();
    }

     /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     * 在mp.weixin.qq.com 开发者中心配置的 URL(服务器地址)  http://域名/index.php/mpbase/weixin/index/mp_id/member_public表的mp_id.html
     */
	public function index($mp_id = '') {

        //设置当前上下文的公众号mp_id
        $mp_id = get_mpid($mp_id);

        $weObj = $this->weObj;
        $weObj->valid();
        $weObj->getRev();
        $data = $weObj->getRevData();
        $type = $weObj->getRevType();
        $ToUserName = $weObj->getRevTo();
        $FromUserName = $weObj->getRevFrom();
        $params['weObj'] = &$weObj;
        $params['mp_id'] = $mp_id;

        if (config('app_debug')) { // 是否开发者模式
            $mid = 0;
            addWeixinLog ( $data, Request::instance()->request(false),$mp_id, $mid ,$ToUserName,$FromUserName,$type);
        }
        //如果被动响应可获得用户信息就记录下
        if (! empty ( $ToUserName )) {
            get_token ( $ToUserName );
        }
        if (! empty ( $FromUserName )) {
            get_openid($FromUserName);
        }

        hook('init_ucuser',$params);   //执行addons/ucuser/Ucuser/init_ucuser的方法,初始化公众号粉丝信息

        $map['openid'] = get_openid();
        $map['mp_id'] = $params['mp_id'];
        $ucuser = model('Ucuser');
        $user = $ucuser->where($map)->find();       //查询出公众号的粉丝
        $fsub = $user["subscribe"];               //记录首次关注状态
        $mid = $user["mid"];
        //与微信交互的中控服务器逻辑可以自己定义，这里实现一个通用的
        switch ($type) {
            //事件
            case TPWechat::MSGTYPE_EVENT:         //先处理事件型消息
                $event = $weObj->getRevEvent();

                switch ($event['event']) {
                    //关注
                    case TPWechat::EVENT_SUBSCRIBE:

                        //二维码关注
                        if(isset($event['eventkey']) && isset($event['ticket'])){

                            //普通关注
                        }else{

                        }

//                        $weObj->reply();
                        //获取回复数据
                        $where['mp_id']=get_mpid();
                        $where['mtype']= 1;
                        $where['statu']= 1;
                        $model_re = model('Mpbase/ReplayMessages');
                        $data_re=$model_re->where($where)->find();
                        $params['type']=$data_re['type'];

                        if(!$data_re){
                            $params['replay_msg']=model('Mpbase/Autoreply')->get_type_data($data_re);
                            $model_re->wxmsg($params);
                        }

//                        hook('wxmsg',$params);

                        $weObj->reply();

                    if(!$user["subscribe"]){   //未关注，并设置关注状态为已关注
                        $user["subscribe"] = 1;     
                        $ucuser->where($map)->update($user);
                    }

                        exit;
			break;
                    //扫描二维码
                    case TPWechat::EVENT_SCAN:

                        break;
                    //地理位置
                    case TPWechat::EVENT_LOCATION:

                        break;
                    //自定义菜单 - 点击菜单拉取消息时的事件推送
                    case TPWechat::EVENT_MENU_CLICK:

//                        hook('keyword',$params);   //把消息分发到实现了keyword方法的addons中,参数中包含本次用户交互的微信类实例和公众号在系统中id
//                        $weObj->reply();           //在addons中处理完业务逻辑，回复消息给用户
                        $where['keywork']=array('like', '%' . $data['Content'] . '%');
                        $where['mtype']= 3;
                        $where['statu']= 1;
                        $where['mp_id']=get_mpid();
                        $model_re = model('Mpbase/ReplayMessages');
                        $data_re=$model_re->where($where)->find();
                        $params['type']=$data_re['type'];

                        if($data_re) {
                            $params['replay_msg'] = model('Autoreply')->get_type_data($data_re);
                            $model_re->wxmsg($params);
                        }
//                        hook('wxmsg',$params);

                        $weObj->reply();  //在addons中处理完业务逻辑，回复消息给用户
                        break;

                    //自定义菜单 - 点击菜单跳转链接时的事件推送
                    case TPWechat::EVENT_MENU_VIEW:

                        break;
                    //自定义菜单 - 扫码推事件的事件推送
                    case TPWechat::EVENT_MENU_SCAN_PUSH:

                        break;
                    //自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
                    case TPWechat::EVENT_MENU_SCAN_WAITMSG:

                        break;
                    //自定义菜单 - 弹出系统拍照发图的事件推送
                    case TPWechat::EVENT_MENU_PIC_SYS:

                        break;
                    //自定义菜单 - 弹出拍照或者相册发图的事件推送
                    case TPWechat::EVENT_MENU_PIC_PHOTO:

                        break;
                    //自定义菜单 - 弹出微信相册发图器的事件推送
                    case TPWechat::EVENT_MENU_PIC_WEIXIN:

                        break;
                    //自定义菜单 - 弹出地理位置选择器的事件推送
                    case TPWechat::EVENT_MENU_LOCATION:

                        break;
                    //取消关注
                    case TPWechat::EVENT_UNSUBSCRIBE:
                    if($user["subscribe"]){
                        $user["subscribe"] = 0;     //取消关注设置关注状态为取消
                        $ucuser->where($map)->update($user);
                    }

                        break;
                    //群发接口完成后推送的结果
                    case TPWechat::EVENT_SEND_MASS:

                        break;
                    //模板消息完成后推送的结果
                    case TPWechat::EVENT_SEND_TEMPLATE:

                        break;
                    default:

                        break;
                }
                break;
            //文本
            case TPWechat::MSGTYPE_TEXT :

                $where['keywork']=array('like', '%' . $data['Content'] . '%');
                $where['mtype']= 3;
                $where['statu']= 1;
                $where['mp_id']=get_mpid();
                $model_re = model('Mpbase/ReplayMessages');
                $data_re=$model_re->order('time desc')->where($where)->find();
//              关键字匹配失败进入自动回复
                if($data_re){
                    unset($where);
                    $where['mtype']= 2;
                    $where['statu']= 1;
                    $where['mp_id']=get_mpid();
                    $data_re=$model_re->order('time desc')->where($where)->find();
                }

                $params['type']=$data_re['type'];

                if($data_re) {
                    $params['replay_msg'] = model('Autoreply')->get_type_data($data_re);
                    $model_re->wxmsg($params);
                }
//                hook('wxmsg',$params);

                $weObj->reply();  //在addons中处理完业务逻辑，回复消息给用户
                break;
            //图像
            case TPWechat::MSGTYPE_IMAGE :

                break;
            //语音
            case TPWechat::MSGTYPE_VOICE :

                break;
            //视频
            case TPWechat::MSGTYPE_VIDEO :

                break;
            //位置
            case TPWechat::MSGTYPE_LOCATION :

                break;
            //链接
            case TPWechat::MSGTYPE_LINK :

                break;
            default:

                break;
        }

        // 记录日志

        if (config('app_debug')) { // 是否开发者模式
            addWeixinLog ( $data, Request::instance()->request(false),$mp_id, $mid ,$ToUserName,$FromUserName,$type);
        }
	}



	/*
	 * 微信支付统一回调接口 后续逻辑可查看 PayNotifyCallBackController 中 NotifyProcess() 说明
	 */
	public function notify(){
		$rsv_data = $GLOBALS ['HTTP_RAW_POST_DATA'];
		$result   = xmlToArray($rsv_data);
		addWeixinLog(var_export($rsv_data, true), $GLOBALS ['HTTP_RAW_POST_DATA']);
		//回复公众平台支付结果
		$notify       = new PayNotifyCallBackController();
		$map["appid"] = $result["appid"];
		$map["mchid"] = $result["mch_id"];
		$info         = model('member_public')->where($map)->find();
		//获取公众号信息，jsApiPay初始化参数
		$cfg = array(
			'APPID'      => $info['appid'],
			'MCHID'      => $info['mchid'],
			'KEY'        => $info['mchkey'],
			'APPSECRET'  => $info['secret'],
			'NOTIFY_URL' => $info['notify_url'],
		);
		WxPayConfig::setConfig($cfg);
		$notify->Handle(false);
	}

	/*
	 * 查询微信支付的订单
	 * 注意 这里未做权限判断
	 */
	public function orderquery()
	{
		$id = input('id','','intval');
		$order  = model("Order");
		if(empty($id)||!($odata = $order->where('id = '. $id )->find()))
		{
			$this->error('该支付记录不存在');
		}
		$map["mp_id"] = $odata["mp_id"];
		$info         = model('member_public')->where($map)->find();
		//获取公众号信息，jsApiPay初始化参数
		$cfg = array(
			'APPID'      => $info['appid'],
			'MCHID'      => $info['mchid'],
			'KEY'        => $info['mchkey'],
			'APPSECRET'  => $info['secret'],
			'NOTIFY_URL' => $info['notify_url'],
		);
		WxPayConfig::setConfig($cfg);
		$input = new WxPayOrderQuery();
		$input->SetOut_trade_no($odata['order_id']);
		$result = WxPayApi::orderQuery($input);
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& array_key_exists("trade_state", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS"
			&& $result["trade_state"] == "SUCCESS")
		{
			// $odata['module'] = Shop 则在D('ShopOrder','Logic')->AfterPayOrder() 内处理后续逻辑
			$class = parse_res_name($odata['module'].'/'.$odata['module'].'Order','Logic');
			if(class_exists($class) &&
				method_exists($class,'AfterPayOrder'))
			{
				$m = new $class();
				$m->AfterPayOrder($result,$odata);
			}
			$this->success('已支付');
		}
		$this->error((empty($result['trade_state_desc'])?'未支付':$result['trade_state_desc']));
	}
}