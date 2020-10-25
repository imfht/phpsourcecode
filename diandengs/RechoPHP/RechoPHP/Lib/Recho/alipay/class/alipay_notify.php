<?php
/*
 *类名：alipay_notify
 *功能：付款过程中服务器通知类
 *详细：该页面是通知返回核心处理文件，不需要修改
 *版本：3.1
 *修改日期：2010-10-29
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

////////////////////注意/////////////////////////
//调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
/////////////////////////////////////////////////

defined('IS_IN') or die('Include Error!');
require_once( RC_PATH_LIB . "alipay/class/alipay_function.php");
class alipay_notify {
    var $gateway_;           //网关地址
    var $_key_;		    //安全校验码
    var $partner_;           //合作伙伴ID
    var $sign_type_;         //签名方式 系统默认
    var $mysign_;            //签名结果
    var $_input_charset_;    //字符编码格式
    var $transport_;         //访问模式

    /**
     * 构造函数（从配置文件中初始化变量）
     * @param <type> $partner		合作身份者ID
     * @param <type> $key		安全校验码
     * @param <type> $sign_type		签名类型
     * @param <type> $_input_charset	字符编码格式
     * @param <type> $transport		访问模式
     */
    function alipay_notify($partner,$key,$sign_type,$_input_charset = "utf-8"/*GBK*/,$transport= "https") {
        $this->transport_ = $transport;
        if($this->transport_ == "https") {
            $this->gateway_ = "https://www.alipay.com/cooperate/gateway.do?";
        }else {
            $this->gateway_ = "http://notify.alipay.com/trade/notify_query.do?";
        }
        $this->partner_ = $partner;
        $this->_key_ = $key;
        $this->mysign_ = "";
        $this->sign_type_ = $sign_type;
        $this->_input_charset_ = $_input_charset;
    }

    /**
     * 对notify_url的认证
     * @return <bool> true/false
     */
    function notify_verify() {
        //获取远程服务器ATN结果，验证是否是支付宝服务器发来的请求
        if($this->transport_ == "https") {
            $veryfy_url = $this->gateway_. "service=notify_verify" ."&partner=" .$this->partner. "&notify_id=".$_POST["notify_id"];
        } else {
            $veryfy_url = $this->gateway_. "partner=".$this->partner_."&notify_id=".$_POST["notify_id"];
        }
        $veryfy_result = $this->get_verify($veryfy_url);

        //生成签名结果
	if(empty($_POST)) return false;
	else {
	    $post = para_filter($_POST);//对所有POST返回的参数去空
	    $sort_post = arg_sort($post);//对所有POST反馈回来的数据排序
	    $this->mysign_ = build_mysign($sort_post,$this->_key_,$this->sign_type_); //生成签名结果
	
	    //写日志记录
	    //log_result("veryfy_result=".$veryfy_result."\n notify_url_log:sign=".$_POST["sign"]."&mysign=".$this->mysign_.",".create_linkstring($sort_post));
	
	    //判断veryfy_result是否为ture，生成的签名结果mysign与获得的签名结果sign是否一致
	    //$veryfy_result的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
	    //mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
	    if (preg_match("/true$/i",$veryfy_result) && $this->mysign_ == $_POST["sign"]) return true;
	    else return false;
	}
    }

    /**
     * 对return_url的认证
     * @return <bool> true/false
     */
    function return_verify() {
        //获取远程服务器ATN结果，验证是否是支付宝服务器发来的请求
	if($this->transport_ == "https") {
            $veryfy_url = $this->gateway_. "service=notify_verify" ."&partner=" .$this->partner_. "&notify_id=".$_GET["notify_id"];
        } else {
            $veryfy_url = $this->gateway_. "partner=".$this->partner_."&notify_id=".$_GET["notify_id"];
        }
        $veryfy_result = $this->get_verify($veryfy_url);

        //生成签名结果
	if(empty($_GET)) return false;
	else {
	    $get = para_filter($_GET);	    //对所有GET反馈回来的数据去空
	    $sort_get = arg_sort($get);	    //对所有GET反馈回来的数据排序
	    $this->mysign_ = build_mysign($sort_get,$this->_key_,$this->sign_type_);    //生成签名结果
	
	    //写日志记录
	    //log_result("veryfy_result=".$veryfy_result."\n return_url_log:sign=".$_GET["sign"]."&mysign=".$this->mysign_."&".create_linkstring($sort_get));
	
	    //判断veryfy_result是否为ture，生成的签名结果mysign与获得的签名结果sign是否一致
	    //$veryfy_result的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
	    //mysign与sign不等，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
	    if (preg_match("/true$/i",$veryfy_result) && $this->mysign_ == $_GET["sign"]) return true;
	    else return false;
	}
    }

    /**
     * 获取远程服务器ATN结果
     * @param <type> $url	指定URL路径地址
     * @param <type> $time_out	超时设置
     * @return <type>		服务器ATN结果集
     */
    function get_verify($url,$time_out = "60") {
	$urlarr = parse_url($url);
        $errno = "";
        $errstr = "";
        $transports = "";
        if($urlarr["scheme"] == "https") {
	    $transports = "ssl://";
            $urlarr["port"] = "443";
        } else {
            $transports = "tcp://";
            $urlarr["port"] = "80";
        }
        $fp=@fsockopen($transports . $urlarr['host'],$urlarr['port'],$errno,$errstr,$time_out);
        if(!$fp) {
            die("ERROR: $errno - $errstr<br />\n");
        } else {
            fputs($fp, "POST ".$urlarr["path"]." HTTP/1.1\r\n");
            fputs($fp, "Host: ".$urlarr["host"]."\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ".strlen($urlarr["query"])."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlarr["query"] . "\r\n\r\n");
            while(!feof($fp)) {
                $info[]=@fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",",$info);
            return $info;
        }
    }
}
?>