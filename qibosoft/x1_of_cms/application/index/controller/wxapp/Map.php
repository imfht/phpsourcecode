<?php
namespace app\index\controller\wxapp;


use app\common\controller\IndexBase;

//小程序  
class Map extends IndexBase{
    
    /**
     * 根据坐标获取街道名
     * @param string $xy
     * @return void|unknown|\think\response\Json
     */
    public function get_address($xy='113.330485,23.108449'){
        list($y,$x) = explode(',',$xy);
        $string = file_get_contents('https://api.map.baidu.com/geocoder/v2/?callback=renderReverse&location='.$x.','.$y.'&output=xml&pois=1&ak=MGdbmO6pP5Eg1hiPhpYB0IVd');
        $postObj = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = json_encode($postObj);
        $jsonArray = json_decode($jsonStr,true);
        $address = $jsonArray['result']['formatted_address'];
        return $this->ok_js([
            'address'=>$address,
            'more'=>$jsonArray['result'],
        ]);
    }
}
