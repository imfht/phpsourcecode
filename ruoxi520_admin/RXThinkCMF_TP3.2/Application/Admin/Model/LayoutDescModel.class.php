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
 * 布局描述-模型
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class LayoutDescModel extends CBaseModel {
    function __construct() {
        parent::__construct('layout_desc');
    }
    
    //自动验证
    protected $_validate = array(
        array('loc_desc', 'require', '页面位置描述不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('loc_desc', '1,100', '页面位置描述长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('loc_id', 'require', '页面位置编号不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('page_id', 'require', '请选择页面位置！', self::EXISTS_VALIDATE, '', 3),
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
            
            //获取站点信息
            if($info['page_id']) {
                $itemMod = M("item");
                $itemInfo = $itemMod->find($info['page_id']);
                $info['page_name'] = $itemInfo['name'];
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级列表
     *
     * @author 牧羊人
     * @date 2018-07-17
     */
    function getChilds($id){
        $result = $this->where(['page_id'=>$id,'mark'=>1])->order("sort_order asc")->select();
        $list = array();
        if($result){
            foreach ($result as $val){
                $info = $this->getInfo($val['id']);
                $list[] = $info ;
            }
        }
        return $list;
    }
    
}