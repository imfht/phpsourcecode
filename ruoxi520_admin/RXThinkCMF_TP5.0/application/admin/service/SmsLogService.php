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
 * 短信日志-服务类
 * 
 * @author 牧羊人
 * @date 2019-02-14
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\SmsLogModel;
class SmsLogService extends AdminServiceModel
{
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    function initialize()
    {
        parent::initialize();
        $this->model = new SmsLogModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-02-14
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
            $map['mobile'] = array('like',"%{$mobile}%");
        }
        
        //发送日期
        $send_date = $param['send_date'];
        if($send_date) {
            $itemArr = explode(' - ', $send_date);
        
            $stime = strtotime(date("Y-m-d 0:00:00",strtotime($itemArr[0])));
            $etime = strtotime(date("Y-m-d 23:59:59",strtotime($itemArr[1])));
            $map['add_time'] = array('between',array($stime,$etime));
        }
        
        return parent::getList($map);
    }
    
}