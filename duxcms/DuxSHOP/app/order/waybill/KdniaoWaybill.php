<?php
namespace app\order\waybill;
/**
 * 快递鸟查询
 */
class KdniaoWaybill extends \app\base\service\BaseService {


    /**
     * 查询快递
     * @param $name
     * @param $label
     * @param $number
     * @return bool
     */
    public function query($name, $label, $number) {
        $config = target('order/OrderConfigWaybill')->getConfig('kdniao');
        if(empty($config)){
            return $this->error('配置不存在!');
        }
        $id = $config['id'];
        $key = $config['key'];
        $requestData = json_encode([
            'OrderCode' => '',
            'ShipperCode' => $label,
            'LogisticCode' => $number,
        ]);
        $data = [
            'EBusinessID' => $id,
            'RequestType' => 1002,
            'RequestData' => urlencode($requestData),
            'DataType' => 2
        ];
        $data['DataSign'] = urlencode(base64_encode(md5($requestData.$key)));

        $raw = \dux\lib\Http::doPost('http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx', $data, 5);

        if(empty($raw)) {
            return $this->error('暂无物流信息');
        }

        $json = json_decode($raw, true);

        if(empty($json['Traces'])) {
            return $this->error($json['Reason'] ? $json['Reason'] : '暂无物流信息');
        }

        $list = $json['Traces'];
        $traces = [];
        foreach($list as $vo) {
            $traces[] = [
                'date' => $vo['AcceptTime'],
                'msg' => $vo['AcceptStation']
            ];
        }
        $traces = array_reverse($traces);
        return $this->success($traces);
    }



}