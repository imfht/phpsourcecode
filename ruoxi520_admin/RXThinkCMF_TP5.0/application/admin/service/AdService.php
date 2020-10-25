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
 * 广告-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\AdModel;
class AdService extends AdminServiceModel
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
        $this->model = new AdModel();
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
        $param = input("request.");
        
        $map = [];
        
        //查询条件
        $title = trim($param['title']);
        if($title) {
            $map['title'] = array('like',"%{$title}%");
        }
        
        return parent::getList($map);
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
        $id = (int)$data['id'];
        $cover = trim($data['cover']);
        $type = (int)$data['type'];
        $t_type = (int)$data['t_type'];
        $type_id = (int)$data['type_id'];
        
        //头像验证
        if(!$id && ($t_type!=2 && !$cover)) {
            return message('请上传广告封面',false);
        }
        
        if($t_type==4 && (!$type || !$type_id)) {
            return message('请选择推荐的类型',false);
        }
        
        //数据处理
        if(strpos($cover, "temp")) {
            $data['cover'] = \Common::saveImage($cover, 'ad');
        }
        if($t_type==4) {
            $data['type'] = $type;
            $data['type_id'] = $type_id;
        }else{
            $data['type'] = 0;
            $data['type_id'] = 0;
        }
        return parent::edit($data);
    }
    
}