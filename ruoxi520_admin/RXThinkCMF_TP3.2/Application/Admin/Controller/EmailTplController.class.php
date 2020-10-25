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
 * 邮件模板-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Controller;
use Admin\Model\EmailTplModel;
use Admin\Service\EmailTplService;
class EmailTplController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new EmailTplModel();
        $this->service = new EmailTplService();
    }
}