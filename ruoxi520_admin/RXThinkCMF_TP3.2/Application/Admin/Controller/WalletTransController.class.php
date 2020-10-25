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
 * 钱包流水-控制器
 * 
 * @author 牧羊人
 * @date 2019-01-08
 */
namespace Admin\Controller;
use Admin\Model\WalletTransModel;
use Admin\Service\WalletTransService;
class WalletTransController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new WalletTransModel();
        $this->service = new WalletTransService();
    }
}