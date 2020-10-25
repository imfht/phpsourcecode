<?php
/**
 * 地理位置处理方法
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm;
class Location {

    /**
     * 计算距离
     * 
     * @return float
     */
    static public function distance($x1, $y1, $x2, $y2) {
        
        $x_length = max($x1, $x2) - min($x1, $x2);
        $y_length = max($y1, $y2) - min($y1, $y2);
        
        $result = sqrt(pow($x_length, 2) + pow($y_length, 2)) * 111.11;
        return $result;
    }
    
}
