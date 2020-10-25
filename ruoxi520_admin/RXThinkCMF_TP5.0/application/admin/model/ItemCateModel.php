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
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
class ItemCateModel extends BaseModel
{
    // 设置数据表
    protected $name = 'item_cate';
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\common\model\BaseModel::getInfo()
     */
    function getInfo($id)
    {
        $info = parent::getInfo($id);
        if($info) {
            
            // 获取上级目录
            if($info['parent_id']) {
                $pInfo = $this->getInfo($info['parent_id']);
                $info['parent_name'] = $pInfo['name'];
            }
            
            // 封面地址
            if($info['cover']) {
                $info['cover_url'] = IMG_URL . $info['cover'];
            }
            
            // 获取站点
            if($info['item_id']) {
                $itemMod = new ItemModel();
                $itemInfo = $itemMod->getInfo($info['item_id']);
                $info['item_name'] = $itemInfo['name'];
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级栏目
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function getChilds($itemId=0, $parentId, $flag=false)
    {
        $map = [
            'parent_id'=>$parentId,
            'mark'=>1,
        ];
        if($itemId) {
            $map['item_id'] = $itemId;
        }
        $result = $this->where($map)->order("sort_order asc")->select();
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
     * 获取栏目名称
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function getCateName($cateId, $delimiter="")
    {
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