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
 * 友情链接-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\LinkModel;
class LinkService extends AdminServiceModel
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
        $this->model = new LinkModel();
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
     * @date 2018-12-13
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::edit()
     */
    function edit()
    {
        $data = input('post.', '', 'trim');
        $data['is_show'] = (isset($data['is_show']) && $data['is_show']=="on") ? 1 : 2;
        $logo = trim($data['logo']);
        $t_type = (int)$data['t_type'];
        
        //字段验证
        if(!$data['id'] && $t_type==2 && !$logo) {
            return message('请上传图片',false);
        }
        if($t_type==1) {
            //文字
            $data['logo'] = '';
        }
        
        //图片处理
        if(strpos($logo, "temp")) {
            $data['logo'] = \Common::saveImage($logo,'link');
        }
        
        return parent::edit($data);
    }
    
}