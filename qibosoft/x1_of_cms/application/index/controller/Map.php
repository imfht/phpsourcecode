<?php
namespace app\index\controller;

use app\common\controller\IndexBase;

class Map extends IndexBase
{
    /**
     * 显示地图
     * @param string $xy 百度坐标
     * @param string $title 地址名称
     * @return mixed|string
     */
    public function index($xy='',$title=''){       
        if(empty($xy)){
            $xy = '113.331247,23.156656';
        }
        list($position_x,$position_y) = explode(',',$xy);
        $this->assign('position_x',$position_x);
        $this->assign('position_y',$position_y);
        $this->assign('title',$title);
        return $this->fetch('bdmap');
    }
    
    /**
     * 拾取地图坐标
     * @param string $xy 默认地址
     * @return mixed|string
     */
    public function point($xy=''){
        if(empty($xy)){
            $xy = '113.263661,23.155131';
        }
        $this->assign('map',$xy);
        return $this->fetch();
    }
    
    /**
     * 显示导航
     * @param string $xy 百度坐标
     * @param string $title 地址名称
     * @param string $address 详细地址
     * @param string $phone 联系电话
     * @return mixed|string
     */
    public function nav($xy = '113.331247,23.156656',$title='',$address='',$phone=''){
        $type = 'bdnav';
        if(in_weixin()){
            $type = 'wxnav';
            //百度转谷歌坐标,因为微信地图用的是谷歌坐标
            $obj  = new \app\common\util\MapGps();
            list($latitude,$longitude) = explode(',',$xy);
            $po = $obj->bd_decrypt($longitude, $latitude);
            $longitude = $po['lon'];
            $latitude = $po['lat'];
            $this->assign('x',$latitude);
            $this->assign('y',$longitude);
        }else{
            $strcode = file_get_contents("http://api.map.baidu.com/geoconv/v1/?coords=".$xy."&ak=MGdbmO6pP5Eg1hiPhpYB0IVd&from=5&to=6&output=json&qq-pf-to=pcqq.c2c");
            $data = json_decode($strcode);
            $result=$data->result;
            
            $mobilemap_x=$result[0]->x;
            $mobilemap_y=$result[0]->y;
            $this->assign('x',$mobilemap_x);
            $this->assign('y',$mobilemap_y);
        }
        $info = [
                'title'=>$title,
                'address'=>$address,
                'telphone'=>$phone,
        ];
        $this->assign('info',$info);
        return $this->fetch($type);
    }
}

?>