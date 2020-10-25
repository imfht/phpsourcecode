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
 * 站点栏目挂件
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Widget;
use Think\Controller;
use Admin\Model\ItemCateModel;
class ItemWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 站点(栏目)选择
     *
     * @author 牧羊人
     * @date 2018-07-16
     */
    function itemSelect($itemId,$cateId=0,$limit=1) {
        
        //创建数组
        $itemList = array(
            1 => array('tname'=>'站点', 'code'=>'item','list'=>array()),
            2 => array('tname'=>'一级栏目', 'code'=>'p_cate','list'=>array()),
            3 => array('tname'=>'二级栏目', 'code'=>'cate','list'=>array()),
        );
        
        //获取栏目
        $itemCateMod = new ItemCateModel();
        $info = $itemCateMod->getInfo($cateId);
        $level = $limit>1 ? $limit : 1;
        $parentId = (int)$info['parent_id'];
        while ($level > 1 && $cateId) {
            $itemList[$level]['list'] = $itemCateMod->getChilds($itemId,$parentId);
            $itemList[$level]['selected'] = $info['id'];
            $info = $itemCateMod->getInfo($parentId);
            $parentId = (int)$info['parent_id'];
            $level--;
        }
        
        //获取站点
        $list = M("item")->where(['status'=>1,'mark'=>1])->select();
        $itemList[1]['list']  = $list;
        $itemList[1]['selected'] = $itemId;
        
        //数组处理
        $itemList = array_slice($itemList, 0, $limit);
        $this->assign('itemList', $itemList);
        $this->display("Item:item.select");
        
    }
    
}