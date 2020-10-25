<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/9
 * Time: 上午11:14
 */

namespace App\Libs;

use Illuminate\Support\Facades\Log;

/**
 * 快递鸟接口
 * Class Sms
 * @package App\Libs
 */
class Kdniao
{

    public function __construct()
    {
        $this->api_domain = 'http://api.kdniao.com/api';
        $this->subscribe_url = $this->api_domain . '/dist';
        //$this->order_url = $this->api_domain . '/EOrderService';//正式上线再开启
        $this->order_url = 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json';//正式上线再开启
    }

    /**
     * 订阅物流消息
     * @param $company_code 物流公司编号
     * @param $code 快递单号
     */
    public function subscribe($company_code, $code)
    {
        $data = array(
            'ShipperCode' => $company_code,
            'LogisticCode' => $code,
        );
        $data = json_encode($data);
        $kdniao_id = config('other.kdniao.id');
        $sign = $this->sign($data);
        $request_data = array(
            'RequestData' => $data,
            'EBusinessID' => $kdniao_id,
            'RequestType' => 1008,
            'DataSign' => $sign,
            'DataType' => 2,
        );

        $res = $this->sendPost($this->subscribe_url, $request_data);
        Log::channel('kdniao_subscribe')->info($data . $res);
        $res = json_decode($res, true);
        if ($res['Success'] === true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 电子面单下单
     * @param $order 订单信息
     * @param $express_company 快递公司信息
     * @param $address 发货地址
     * @return array
     */
    public function order($order, $express_company, $address)
    {
        $data = array(
            'MemberID' => $order['seller_id'],//商家id
            'ShipperCode' => $express_company['code'],//快递公司编号
            'ThrOrderCode' => $order['order_no'],//第三方单号
            'OrderCode' => $order['order_no'],//订单号
            'PayType' => $express_company['pay_type'],//邮费支付方式:1-现付，2-到付，3-月结，4-第三方支付(仅SF支持)
            'ExpType' => 1,//快递类型：1-标准快件 ,详细快递类型参考《快递公司快递业务类型.xlsx》
            'Receiver' => array(
                'Name' => $order['full_name'],
                'Mobile' => $order['tel'],
                'ProvinceName' => $order['prov'],
                'CityName' => $order['city'],
                'ExpAreaName' => $order['area'] ? $order['area'] : $order['city'],
                'Address' => $order['address']
            ),
            'Sender' => array(
                'Name' => $address['full_name'],
                'Mobile' => $address['tel'],
                'ProvinceName' => $address['prov_name'],
                'CityName' => $address['city_name'],
                'ExpAreaName' => $address['area_name'] ? $address['area_name'] : $address['city_name'],
                'Address' => $address['address']
            ),
            'Quantity' => 1,//包裹数量
            //商品信息
            'Commodity' => array(
                array(
                    'GoodsName' => '订单号：' . $order['order_no']
                )
            ),
            'IsReturnPrintTemplate' => 1,//返回电子面单模板：0-不需要；1-需要
        );
        if ($express_company['customer_name']) $data['CustomerName'] = $express_company['customer_name'];
        if ($express_company['customer_pwd']) $data['CustomerPwd'] = $express_company['customer_pwd'];
        if ($express_company['send_site']) $data['SendSite'] = $express_company['send_site'];
        if ($express_company['month_code']) $data['MonthCode'] = $express_company['month_code'];

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $kdniao_id = config('other.kdniao.id');
        $sign = $this->sign($data);
        $request_data = array(
            'RequestData' => urlencode($data),
            'EBusinessID' => $kdniao_id,
            'RequestType' => 1007,
            'DataSign' => $sign,
            'DataType' => 2,
        );
        $res = $this->sendPost($this->order_url, $request_data);
        $res = json_decode($res, true);
        if ($res['Success'] === true) {
            $return = array(
                'status' => true,
                'code' => $res['Order']['LogisticCode'],
                'print_template' => $res['PrintTemplate']
            );
        } else {
            $return = array(
                'status' => false,
                'msg' => $res['Reason']
            );
        }
        return $return;
    }

    /**
     * post提交数据
     * @param string $url 请求Url
     * @param array $datas 提交的数据
     * @return url响应返回的html
     */
    public function sendPost($url, $datas)
    {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader .= "Host:" . $url_info['host'] . "\r\n";
        $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader .= "Connection:close\r\n\r\n";
        $httpheader .= $post_data;
        $port = isset($url_info['port']) ? $url_info['port']: 80;
        $fd = fsockopen($url_info['host'], $port);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets .= fread($fd, 128);
        }
        fclose($fd);
        return $gets;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    function sign($data)
    {
        $appkey = config('other.kdniao.key');
        return urlencode(base64_encode(md5($data . $appkey)));
    }
}