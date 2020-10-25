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
 * 城市挂件
 * 
 * @author 牧羊人
 * @date 2018-12-12
 */
namespace app\admin\widget;
use app\admin\model\CityModel;
class CityWidget extends BaseWidget
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new CityModel();
    } 
    
    /**
     * 选择城市【常规模式】
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function simpleSelect($cityId, $limit=3)
    {
        $cityList = array(
            1 => array('tname'=>'省', 'code'=>'province'),
            2 => array('tname'=>'市', 'code'=>'city'),
            3 => array('tname'=>'县/区', 'code'=>'district'),
        );
        $info = $this->model->getInfo($cityId);
        $level = $info['level'];
        $cityList[1]['list']  = $this->model->getChilds(1);
        while ($level > 1) {
            $cityList[$level]['list'] = $this->model->getChilds($info['parent_id']);
            $cityList[$level]['selected'] = $info['id'];
            $info = $this->model->getInfo($info['parent_id']);
            $level--;
        }
        $cityList[1]['selected'] = $info['id'];
        $cityList = array_slice($cityList, 0, $limit);
        $this->assign('cityList', $cityList);
        return $this->fetch('city/simple_select');
    }
    
    /**
     * 城市选择【复杂模式】
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function complexSelect($param, $selectId, $limit=3)
    {
        $arr = explode('|', $param);
        
        // 提示文字
        $msg = $arr[0];
        // 是否必填
        $isV = $arr[1];
        // 层级数组
        $level = [
            1 => 'province',
            2 => 'city',
            3 => 'district',
        ];
        
        $cityMod = new CityModel();
        $cityName = $cityMod->getCityName($selectId," ");
        $itemArr = explode(' ', $cityName);
        
        $this->assign('msg',$msg);
        $this->assign('isV',$isV);
        $this->assign('level',$level[$limit]);
        $this->assign('item',$itemArr);
        return $this->fetch('city/complex_select');
    }
    
}