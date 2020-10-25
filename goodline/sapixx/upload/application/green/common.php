<?php
/**
 * 百度地图---->腾讯地图
 * @param double $lat 纬度
 * @param double $lng 经度
 * @return array();
 */
function bdMap_to_txMap($lat,$lng){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng - 0.0065;
    $y = $lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta);
    $lat = $z * sin($theta);
    return array('lng'=>$lng,'lat'=>$lat);
}
 

/**
 * 腾讯地图---->百度地图
 * @param double $lat 纬度
 * @param double $lng 经度
 */
function txMap_to_bdMap($lat,$lng){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng;
    $y = $lat;
    $z =sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta) + 0.0065;
    $lat = $z * sin($theta) + 0.006;
    return array('lng'=>$lng,'lat'=>$lat);
}