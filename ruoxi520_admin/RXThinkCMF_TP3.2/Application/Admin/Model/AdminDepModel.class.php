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
 * 部门-模型
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class AdminDepModel extends CBaseModel {
    function __construct() {
        parent::__construct('admin_dep');
    }
    
    //自动验证
    protected $_validate = array(
        array('name', '1,50', '部门名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-17
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
            
            //权限反序列化
            if($info['auth']) {
                $info['auth'] = unserialize($info['auth']);
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级
     *
     * @author 牧羊人
     * @date 2018-03-06
     */
    function getChilds($parentId,$flag=false) {
        $cond = [
            'parent_id' =>$parentId,
            'mark' =>1,
        ];
        $result = $this->where($cond)->order("sort_order asc")->select();
        if($result) {
            foreach ($result as $val) {
                $id = (int)$val['id'];
                $info = $this->getInfo($id);
                if(!$info) continue;
                if($flag) {
                    $childList = $this->getChilds($id,0);
                    $info['children'] = $childList;
                }
                $list[] = $info;
            }
        }
        return $list;
    }
    
    /**
     * 获取部门名称
     *
     * @author 牧羊人
     * @date 2018-07-17
     */
    function getDepName($depId, $delimiter="") {
        do {
            $info = $this->getInfo($depId);
            $names[] = $info['name'];
            $depId = $info['parent_id'];
        } while($depId>1);
        $names = array_reverse($names);
//         if (strpos($names[1], $names[0])===0) {
//             unset($names[0]);
//         }
        return implode($delimiter, $names);
    }
    
}