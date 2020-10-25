<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\service;

use app\admin\model\Dic;
use app\common\service\BaseService;

/**
 * 字典管理-服务类
 * @author 牧羊人
 * @since 2020/7/10
 * Class DicService
 * @package app\admin\service
 */
class DicService extends BaseService
{
    /**
     * 初始化
     * @author 牧羊人
     * @since 2020/7/10
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new Dic();
    }
}
