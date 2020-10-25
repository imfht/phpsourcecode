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
 * 布局描述挂件
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Widget;
use Think\Controller;
class LayoutDescWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 布局描述选择
     *
     * @author 牧羊人
     * @date 2018-07-17
     */
    function select($pageId,$locId,$limit=2){
        $layoutDescArr = array(
            1 => array('tname' => '站点', 'code' => 'page'),
            2 => array('tname' => '页面位置', 'code' => 'loc'),
        );
        $result = M("layoutDesc")->where(['page_id'=>$pageId,'mark'=>1])->getField('id,page_id,loc_id,loc_desc',true);
        $subItem = array();
        if(is_array($result)) {
            foreach ($result as $k => $v){
                $subItem[$v['loc_id']]['id'] = $v['loc_id'];
                $subItem[$v['loc_id']]['name'] = $v['loc_desc'];
            }
        }
    
        //获取站点
        $itemList = M("item")->where(['status'=>1,'mark'=>1])->getField('id,name',true);
        $layoutDescArr[1]['list'] = $itemList;
        $layoutDescArr[1]['selected'] = $pageId;
        $layoutDescArr[2]['list'] = $subItem;
        $layoutDescArr[2]['selected'] = $locId;
        $layoutDescArr = array_slice($layoutDescArr, 0, $limit);
        $this->assign('layoutDescList', $layoutDescArr);
        $this->display("LayoutDesc:layoutDesc.select");
    }
    
}