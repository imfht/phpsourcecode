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
 * 站点栏目-模型
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ItemCateModel extends CBaseModel {
    function __construct() {
        parent::__construct("item_cate");
    }
    
    //自动验证
    protected $_validate = array(
        array('name', 'require', '栏目名称不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('name', '1,15', '栏目名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('item_id', 'require', '请选择站点！', self::EXISTS_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-16
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //获取上级
            if($info['parent_id']) {
                $pInfo = $this->getInfo($info['parent_id']);
                $info['parent_name'] = $pInfo['name'];
            }
            
            //栏目封面
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            //获取站点名称
            if($info['item_id']) {
                $itemMod = new ItemModel();
                $itemInfo = $itemMod->getInfo($info['item_id']);
                $info['item_name'] = $itemInfo['name'];
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级数据
     * 
     * @author 牧羊人
     * @date 2018-07-16
     */
    function getChilds($itemId=0,$parentId,$flag=false) {
        $cond[] = "parent_id={$parentId} and mark=1";
        if($itemId) {
            $cond[] = "item_id={$itemId}";
        }
        $result = $this->where($cond)->order("sort_order asc")->select();
        if($result) {
            foreach ($result as $val) {
                $info = $this->getInfo($val['id']);
                if(!$info) continue;
                if($flag) {
                    $childList = $this->getChilds($itemId,$val['id'],0);
                    $info['children'] = $childList;
                }
                $list[] = $info;
            }
        }
        return $list;
    }
    
    /**
     * 获取分类名称
     *
     * @author 牧羊人
     * @date 2018-07-16
     */
    function getCateName($cateId,$delimiter="") {
        do {
            $info = $this->getInfo($cateId);
            $names[] = $info['name'];
            $cateId = $info['parent_id'];
        } while($cateId>0);
        $names = array_reverse($names);
        if (strpos($names[1], $names[0])===0) {
            unset($names[0]);
        }
        $nameStr = implode($delimiter, $names);
        return $nameStr;
    }
    
}