<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 阿里云API市场接口集合
 */
namespace app\common\widget;
use app\common\model\ConfigApis;
use app\system\model\MemberBank;
use app\system\model\MemberBankBill;
use filter\Filter;
use GuzzleHttp\Client;

class AliApi{

    public function __construct() {
        $this->config = ConfigApis::config('aliapi');
    }

    /**
     * 根据IP地址定位
     */
    public function ip($ip,$member_id = 0){
        if($member_id > 0){
            $rel = self::moneyUpate($member_id);
            if(!$rel){
                return $rel['error_code'] == 0;
            }
        }
        $ip = trim(Filter::filter_escape($ip));
        $ip = $ip == '127.0.0.1' ? '222.88.142.161' : $ip;
        $client = new Client([
            'base_uri' => 'http://ipquery.market.alicloudapi.com',
            'timeout'  => 2.0,
        ]);
        $rel = ConfigApis::config('aliapi');
        $response = $client->request('GET','/query?ip='.$ip,[
            'headers' => [
                'Authorization' => 'APPCODE '.$rel['appcode'],
                'Accept'     => 'application/json',
            ]
        ]);
        return json_decode($response->getBody(),true);
    }

    /**
     * 根据经纬度定位所在城市
     */
    public function gps($latitude,$longitude,$member_id = 0){
        if($member_id > 0){
            $rel = self::moneyUpate($member_id);
            if(!$rel){
                return;
            }
        }
        $latitude  = floatval(Filter::filter_escape($latitude));
        $longitude = floatval(Filter::filter_escape($longitude));
        $client = new Client([
            'base_uri' => 'http://getlocat.market.alicloudapi.com/api/getLocationinfor',
            'timeout'  => 2.0,
        ]);
        $response = $client->request('POST',"?latlng={$latitude}%2C{$longitude}&type=2",[
            'headers' => [
                'Authorization' => 'APPCODE '.$this->config['appcode'],
                'Accept'     => 'application/json',
            ]
        ]);
        $rel = json_decode($response->getBody(),true);
        if($rel['error_code'] == 0){
            return $rel;
        }
        return;
    }
    
    /**
     * 根据地址位置转换经纬度
     */
    public function address($address,$miniapp_id = 0){
        if($miniapp_id > 0){
            $rel = self::moneyUpate($miniapp_id);
            if(!$rel){
                return;
            }
        }
        $address  = Filter::filter_escape($address);
        $client = new Client([
            'base_uri' => 'http://geo.market.alicloudapi.com/v3/geocode/geo',
            'timeout'  => 2.0,
        ]);
        $response = $client->request('GET',"?address={$address}&batch=false&output=JSON",[
            'headers' => [
                'Authorization' => 'APPCODE '.$this->config['appcode'],
                'Accept'     => 'application/json',
            ]
        ]);
        $rel = json_decode($response->getBody(),true);
        if($rel['status'] == 1){
            return $rel;
        }
        return;
    }

    /**
     * 定位费用扣除
     * @param [type] $id
     * @return void
     */
    protected function moneyUpate($member_id){
        if($member_id > 0 && !empty($this->config['price'])){
            $rel = MemberBank::moneyJudge($member_id,$this->config['price']); //判断余额
            if($rel){
                return;
            }
            MemberBankBill::create(['state' => 1,'money' => $this->config['price'],'member_id' => $member_id,'message' => '调用第三方服务收费','update_time' => time()]);
            MemberBank::moneyUpdate($member_id, -$this->config['price']);
            return true;
        }
        return;
    }
}