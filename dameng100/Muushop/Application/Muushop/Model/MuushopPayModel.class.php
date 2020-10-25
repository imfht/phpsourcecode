<?php
namespace Muushop\Model;
use Think\Model;
use Think\Page;


class MuushopPayModel extends Model{

    //支付方式类型
    protected $payment;

    function __construct(){
        $this->payment = array('onlinepay'=>'在线支付','balance'=>'余额','delivery'=>'货到付款');
    }
    /**
    * 判断在线支付方式列表
    */
    public function channel(){
        $path = APP_PATH  . 'Muushop/Conf/channel.php';
        $channel = load_config($path);

        $payChannel = array();

        if(is_mobile()){
            foreach($channel as $k=>$val){
                if(strpos($k,'wap') && $val['status']){
                    $payChannel[] = $val;
                }
            }
        }elseif(isWeixinBrowser()){
            if($k=='wx_wap' && $val['status']){
                $payChannel[] = $val;
            }
        }else{
            foreach($channel as $k=>$val){
                if(strpos($k,'pc') && $val['status']){
                    $payChannel[] = $val;
                }
                if($k=='wx_pub_qr' && $val['status']){
                    $payChannel[] = $val;
                }
                if($k=='alipay_qr' && $val['status']){
                    $payChannel[] = $val;
                }
            }
        }
        return $payChannel;
    }

    /**
     * 根据支付channel获取支付详细配置
     * @param  [type] $channel [description]
     * @return [type]          [description]
     */
    public function getPaychannelInfo($channel)
    {
        $path = APP_PATH  . 'Muushop/Conf/channel.php';
        $config = load_config($path);
        return $config[$channel];
    }

    /**
     * 根据配置数据获取支付方式数组
     * @return [type] [description]
     */
    public function getPayment($payment_config){
        //支付方式类型
        $payment = $this->payment;
        //如果$payment_config留空就返回所有
        if(empty($payment_config)){
            return $payment;
        }
        //获取传入配置数据的支付方式
        $payment_array = explode(',',$payment_config);

        $result = array();
        foreach ($payment_array as $v) {
            # code...
            if(array_key_exists($v,$payment)){
                $result[] = array('title'=>$payment[$v],'value'=>$v);
            }
        }
        return $result;
    }
    /**
     * 通过支付方式英文码获取中文名
     * @param  [type] $title [description]
     * @return [type]        [description]
     */
    public function getPaymentTitle($title){

        //支付方式类型
        $payment = $this->payment;

        return $payment[$title];

    }
    

}