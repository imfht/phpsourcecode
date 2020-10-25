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
 * 分类-模型
 * 
 * @author 牧羊人
 * @date 2018-10-09
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class CateModel extends CBaseModel {
    function __construct() {
        parent::__construct('category');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-09
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //获取上级分类
            if($info['parent_id']) {
                $cateInfo = M("category")->find($info['parent_id']);
                $info['parent_name'] = $cateInfo['name'];
            }
            
            //默认图标
            if($info['icon']) {
                $info['icon_url'] = IMG_URL . $info['icon'];
            }
            
            //选中图标
            if($info['icon2']) {
                $info['icon2_url'] = IMG_URL . $info['icon2'];
            }
            
            //类型
            if($info['type']) {
                $info['type_name'] = C('CATEGORY_TYPE')[$info['type']];
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级数据
     * 
     * @author 牧羊人
     * @date 2018-10-09
     */
    function getChilds($parentId,$flag=false) {
        $list = array();
        $result = $this->where([
            'parent_id' =>$parentId,
            'type'=>1,
            'mark'      =>1
        ])->order("id asc")->select();
        if($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if($flag) {
                    $childList = $this->getChilds($id,$flag);
                    if(is_array($childList)) {
                        $info['children'] = $childList;
                    }
                }
                $list[] = $info;
            }
        }
        return $list;
    }
    
    /**
     * 获取分类
     * 
     * @author 牧羊人
     * @date 2018-11-02
     */
    function getCateName($cateId, $delimiter="") {
        do {
            $info = $this->getInfo($cateId);
            $names[] = $info['name'];
            $cateId = $info['parent_id'];
        } while($cateId>0);
        $names = array_reverse($names);
//         if (strpos($names[1], $names[0])===0) {
//             unset($names[0]);
//         }
        return implode($delimiter, $names);
    }
    
}