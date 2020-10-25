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
 * 标签-组件
 * 
 * @author 牧羊人
 * @date 2018-11-22
 */
namespace Admin\Widget;
use Think\Controller;
class TagsWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 标签选择(cate_id|1|选择标签|name|id|2)
     * 
     * @author 牧羊人
     * @date 2018-11-22
     */
    function select($param,$tagsList=[],$selectId=[]) {
        $arr = explode('|', $param);
        
        //参数
        $idStr = $arr[0];
        $isV = $arr[1]==1 ? 'required' : '';
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
        $max = $arr[5] ? $arr[5] : 5;
        $width = isset($arr[6]) ? $arr[6] : 0;
        
        //数据源处理
        $tagsList = json_encode(array_values($tagsList));
        
        $this->assign('idStr',$idStr);
        $this->assign('isV',$isV);
        $this->assign('msg',$msg);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('max',$max);
        $this->assign('width',$width);
        $this->assign('tagsList',$tagsList);
        $this->assign("selectId",json_encode($selectId));
        $this->display("Widget:tags.select");
    }
    
}