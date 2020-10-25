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
 * 分类属性-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\CateAttributeModel;
class CateAttributeService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new CateAttributeModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-25
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //属性名称
        $name = trim($param['name']);
        if($name) {
            $map['name'] = array('like',"%{$name}%");
        }
        
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-10-16
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $data['type'] = (isset($data['type']) && $data['type']=="on") ? 1 : 2;
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        return parent::edit($data);
    }
    
    /**
     * 获取属性列表
     * 
     * @author 牧羊人
     * @date 2018-10-24
     */
    function getAttributeList($type=2) {
        $map = [
            'type'=>$type,
            'status'=>1,
        ];
        $result = $this->mod->where($map)->order("sort_order ASC")->getField("id,name",true);
        $list = [];
        if(is_array($result)) {
            foreach ($result as $key=>$val) {
                //获取属性值
                $attrValList = M("CategoryAttributeValue")->where([
                    'category_attribute_id'=>$key,
                    'status'=>1,
                ])->select();
                
                $itemArr = [];
                foreach ($attrValList as $vt) {
                    $itemArr[] = [
                        'id'=>(int)$vt['id'],
                        'name'=>$vt['attribute_value'],
                    ];
                }
                
                $list[] = [
                    'id'=>$key,
                    'name'=>$val,
                    'sub'=>$itemArr,
                ];
            }
        }
        return $list;
    }
    
}