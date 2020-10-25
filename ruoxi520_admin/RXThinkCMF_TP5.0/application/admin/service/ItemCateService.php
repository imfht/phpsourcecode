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
 * 栏目-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\ItemCateModel;
class ItemCateService extends AdminServiceModel
{
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    function initialize()
    {
        parent::initialize();
        $this->model = new ItemCateModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::getList()
     */
    function getList()
    {
        $list = $this->model->getChilds(0,0,1);
        return message("操作成功",true,$list);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::edit()
     */
    function edit()
    {
        $data = input('post.', '', 'trim');
        $is_cover = (isset($data['is_cover']) && $data['is_cover']=="on") ? 1 : 2;
        $data['is_cover'] = $is_cover;
        $cover = trim($data['cover']);
        
        //封面验证
        if($is_cover==1 && !$data['id'] && !$cover) {
            return message('请上传栏目封面',false);
        }
        if($is_cover==1) {
            if(strpos($cover, "temp")) {
                $data['cover'] = \Common::saveImage($cover, 'itemCate');
            }
        }else if($is_cover==2){
            $data['cover'] = '';
        }
        return parent::edit($data);
    }
    
    /**
     * 获取子级列表【挂件专用】
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function getChilds()
    {
        $itemId = input("post.item_id",0);
        $pid = input("post.pid",0);
        $result = $this->model->getChilds($itemId,$pid,true);
        $list = [];
        if(is_array($result)) {
            foreach ($result as $val) {
                $list[] = [
                    'id'=>$val['id'],
                    'name'=>$val['name'],
                ];
                foreach ($val['children'] as $vt) {
                    $list[] = [
                        'id'=>$vt['id'],
                        'name'=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--" . $vt['name'],
                    ];
                }
            }
        }
        return message('获取成功',true,$list);
    }
    
}