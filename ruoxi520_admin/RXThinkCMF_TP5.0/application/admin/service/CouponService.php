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
 * 优惠券-服务类
 * 
 * @author 牧羊人
 * @date 2019-01-27
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\CouponModel;
class CouponService extends AdminServiceModel
{
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2019-01-27
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    function initialize()
    {
        parent::initialize();
        $this->model = new CouponModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-01-27
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
     * @date 2019-01-27
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::edit()
     */
    function edit()
    {
        $data = input('post.', '', 'trim');
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        
        //满减金额以分为单位存储
        $data['amount'] = $data['amount']*100;
        
        //优惠券面值
        $data['face_value'] = $data['face_value']*100;
        
        return parent::edit($data);
    }
    
}