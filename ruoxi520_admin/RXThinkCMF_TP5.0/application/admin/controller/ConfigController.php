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
 * 配置-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-14
 */
namespace app\admin\controller;
use app\admin\model\ConfigModel;
use app\admin\service\ConfigService;
class ConfigController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-14
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new ConfigModel();
        $this->service = new ConfigService();
    }
    
}