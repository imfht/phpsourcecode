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
 * @date 2018-12-13
 */
namespace app\admin\model;
use app\common\model\BaseModel;
class LayoutDescModel extends BaseModel
{
    // 设置数据表
    protected $name = 'layout_desc';
    
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
            
            //获取站点信息
            if($info['page_id']) {
                $itemMod = new ItemModel();
                $itemInfo = $itemMod->find($info['page_id']);
                $info['page_name'] = $itemInfo['name'];
            }
            
        }
        return $info;
    }
    
    /**
     * 获取子级数据
     * 
     * @author 牧羊人
     * @date 2018-12-14
     */
    function getChilds($id)
    {
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