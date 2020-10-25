<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 城市选择-挂件
 * 
 * @author 牧羊人
 * @date 2018-07-19
 */
namespace Admin\Widget;
use Think\Controller;
use Admin\Model\CityModel;
class CityWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 选择城市【常规模式】
     * 
     * @author 牧羊人
     * @date 2018-07-19
     */
    function select($cityId,$limit=3) {
        $cityMod = new CityModel();
        $cityList = array(
            1 => array('tname'=>'省', 'code'=>'province'),
            2 => array('tname'=>'市', 'code'=>'city'),
            3 => array('tname'=>'县/区', 'code'=>'district'),
        );
        $info = $cityMod->getInfo($cityId);
        $level = $info['level'];
        $cityList[1]['list']  = $cityMod->getChilds(1);
        while ($level > 1) {
            $cityList[$level]['list'] = $cityMod->getChilds($info['parent_id']);
            $cityList[$level]['selected'] = $info['id'];
            $info = $cityMod->getInfo($info['parent_id']);
            $level--;
        }
        $cityList[1]['selected'] = $info['id'];
        $cityList = array_slice($cityList, 0, $limit);
        $this->assign('cityList', $cityList);
        $this->display("City:city.select");
    }
    
    /**
     * 选择城市【精简模式】
     * 
     * @author 牧羊人
     * @date 2018-11-21
     */
    function select2($param,$selectId,$limit=3) {
        $arr = explode('|', $param);
        
        //提示文字
        $msg = $arr[0];
        //是否必填
        $isV = $arr[1];
        //层级数组
        $level = [
            1 => 'province',
            2 => 'city',
            3 => 'district',
        ];
        
        //获取数据信息
        $cityMod = new CityModel();
        $cityName = $cityMod->getCityName($selectId," ");
        $itemArr = explode(' ', $cityName);
        
        $this->assign('msg',$msg);
        $this->assign('isV',$isV);
        $this->assign('level',$level[$limit]);
        $this->assign('item',$itemArr);
        $this->display("City:city.select2");
    }
    
}