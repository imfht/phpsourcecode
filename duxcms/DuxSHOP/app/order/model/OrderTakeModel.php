<?php

/**
 * 自提点
 */

namespace app\order\model;

use app\system\model\SystemModel;

class OrderTakeModel extends SystemModel
{

    protected $infoModel = [
        'pri' => 'take_id',
    ];

    protected function getLocation($province, $city, $region, $address) {
        $conrent = \dux\lib\Http::doGet('http://apis.map.qq.com/ws/geocoder/v1/?key=DOZBZ-HP634-BLOUM-XYW4E-F4O7S-XZFPX&address=' . $province . $city . $region . $address, 10);
        if (empty($conrent)) {
            $this->error = '地图服务器繁忙！';
            return false;
        }
        $conrent = json_decode($conrent, true);
        if ($conrent['status']) {
            $this->error = $conrent['message'];
            return false;
        }
        $data['lat'] = $conrent['result']['location']['lat'];
        $data['lng'] = $conrent['result']['location']['lng'];
        return $data;
    }

    protected function _saveBefore($data, $type) {

        $location = $this->getLocation($data['province'], $data['city'], $data['region'], $data['address']);
        if(!$location) {
            return false;
        }

        $data['lat'] = $location['lat'];
        $data['lng'] = $location['lng'];

        return $data;

    }

}