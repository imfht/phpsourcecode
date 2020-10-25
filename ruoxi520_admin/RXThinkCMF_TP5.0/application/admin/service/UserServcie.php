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
 * 用户管理-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-14
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\UserModel;
class UserService extends AdminServiceModel
{
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2018-12-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    function initialize()
    {
        parent::initialize();
        $this->model = new UserModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-12-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::getList()
     */
    function getList()
    {
        $param = input("request.");
        
        $map = [];
        
        //手机号码
        $mobile = trim($param['mobile']);
        if($mobile) {
            $map['mobile'] = $mobile;
        }
        
        //类型
        $type = (int)$param['type'];
        if($type) {
            $map['type'] = $type;
        }
        
        return parent::getList($map);
    }
    
    /**
     * 设置会员状态
     * 
     * @author 牧羊人
     * @date 2018-12-14
     */
    function setStatus()
    {
        $data = input('post.', '', 'trim');
        if(!$data['id']) {
            return message('会员ID不能为空',false);
        }
        if(!$data['status']) {
            return message('会员状态不能为空',false);
        }
        return parent::edit($data);
    }
    
}