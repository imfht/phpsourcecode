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
 * 站点选择-挂件
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\widget;
use app\admin\model\ItemCateModel;
class ItemWidget extends BaseWidget
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new ItemCateModel();
    }
    
    /**
     * 站点栏目选择
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function select($itemId, $cateId=0, $limit=1)
    {
        // 初始化数组
        $itemList = array(
            1 => array('tname'=>'站点', 'code'=>'item','list'=>array()),
            2 => array('tname'=>'栏目', 'code'=>'cate','list'=>array()),
        );
        
        // 获取栏目
        $result = $this->model->getChilds($itemId,0,true);
        $cateList = [];
        if(is_array($result)) {
            foreach ($result as $val) {
                $cateList[] = [
                    'id'=>$val['id'],
                    'name'=>$val['name'],
                ];
                foreach ($val['children'] as $vt) {
                    $cateList[] = [
                        'id'=>$vt['id'],
                        'name'=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--" . $vt['name'],
                    ];
                }
            }
        }
        $itemList[2]['list'] = $cateList;
        $itemList[2]['selected'] = $cateId;
        
        // 获取站点
        $list = db("item")->where(['status'=>1,'mark'=>1])->select();
        $itemList[1]['list']  = $list;
        $itemList[1]['selected'] = $itemId;
        
        // 数组处理
        $itemList = array_slice($itemList, 0, $limit);
        $this->assign('itemList', $itemList);
        
        return $this->fetch("item/widget_select");
    }
    
}