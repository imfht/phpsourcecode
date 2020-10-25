<?php
namespace app\common\fun;

class Map{

    /**
     * 统计两点距离
     * @param string $map_a
     * @param string $map_b
     * @param string $type bike walk car
     * @param string $tactics 算路偏好，该参数只对驾车算路(driving)生效。 该服务为满足性能需求，不含道路阻断信息干预。
        可选值：
        10： 不走高速；
        11：常规路线，即多数用户常走的一条经验路线，满足大多数场景需求，是较推荐的一个策略
        12： 距离较短（考虑路况）：即距离相对较短的一条路线，但并不一定是一条优质路线。计算耗时时，考虑路况对耗时的影响；
        13： 距离较短（不考虑路况）：路线同以上，但计算耗时时，不考虑路况对耗时的影响，可理解为在路况完全通畅时预计耗时。 
        注：除13外，其他偏好的耗时计算都考虑实时路况
     */
    public static function distance($map_a='113.264315,23.155475',$map_b='113.342504,23.07331',$type='car',$tactics='11'){        
        if($type=='car'){
            $url = "https://api.map.baidu.com/routematrix/v2/driving?";
        }elseif($type=='bike'){
            $url = "https://api.map.baidu.com/routematrix/v2/riding?";
        }else{
            $url = "https://api.map.baidu.com/routematrix/v2/walking?";
        }
        list($a_x,$a_y) = explode(',',$map_a);
        list($b_x,$b_y) = explode(',',$map_b);
        $url .= "output=json&tactics=11&origins=$a_y,$a_x&destinations=$b_y,$b_x&ak=MGdbmO6pP5Eg1hiPhpYB0IVd";
        $array = json_decode(file_get_contents($url),true);
        if ($array['status']==0 && $array['result'][0]['distance']) {
            $km = $array['result'][0]['distance']['text'];
            if(strstr($km,'公里')){
                $km = str_replace('公里', '', $km);
            }else{
                $km = str_replace('米', '', $km);
                $km = round($km/1000,2);
            }
            return [
                'm'=>$array['result'][0]['distance']['value'],
                'km'=>$km,
                'time'=>$array['result'][0]['duration']['text'],
            ];
        }else{
            return false;
        }
    }
}