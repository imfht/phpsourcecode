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
 * 分类-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-09
 */
namespace Admin\Controller;
use Admin\Model\CateModel;
use Admin\Service\CateService;
use Admin\Model\CateAttributeModel;
use Admin\Model\CateAttributeRelationModel;
class CateController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new CateModel();
        $this->service = new CateService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-10-09
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::edit()
     */
    function edit() {
        $pid = I("get.pid",0);
        if($pid) {
            $cateInfo = M("category")->find($pid);
            $this->assign('parent_name',$cateInfo['name']);
        }
        parent::edit([
            'parent_id'=>$pid,
            'parent_name'=>$cateInfo['name'],
        ]);
    }
    
    /**
     * 分类属性
     *
     * @author 牧羊人
     * @date 2018-12-24
     */
    function cateAttr() {
        if(IS_POST) {
            $message = $this->service->cateAttr();
            $this->ajaxReturn($message);
            return ;
        }
        
        $cateAttrRelationMod = new CateAttributeRelationModel();
        
        // 分类ID
        $categoryId = (int)$_GET['category_id'];
        
        // 获取已关联属性
        $attrList = $cateAttrRelationMod->where([
            'category_id'=>$categoryId,
            'mark'=>1,
        ])->getField('category_attribute_id',true);
        
        // 获取分类属性
        $cateAttrMod = new CateAttributeModel();
        $cateAttrList = $cateAttrMod->where(['type'=>1,'status'=>1])->select();
        if($attrList) {
            foreach ($cateAttrList as &$val) {
                $val['selected'] = in_array($val['id'], $attrList);
            }
        }
        $this->assign('cateAttrList', $cateAttrList);
        $this->assign('category_id',$_GET['category_id']);
        $this->render();
    }
    
    /**
     * 获取子级【挂件专用】
     *
     * @author 牧羊人
     * @date 2018-11-02
     */
    function getChilds() {
        if(IS_POST) {
            $id = I("post.p_cate_id",0);
            $list = $this->mod->getChilds($id);
            $this->ajaxReturn(message('获取成功',true,$list));
        }
    }
    
}