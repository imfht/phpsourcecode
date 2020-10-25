<?php
/**
 * 提醒管理Index Aciton
 * User: liuzimu
 * Date: 2017/10/21
 * Time: 10:57
 */

namespace application\modules\message\actions\notifymanage;

use application\core\utils\Ibos;
use CAction;

class Index extends CAction
{
    public function run()
    {
        $this->controller->setPageTitle(Ibos::lang('Notify Manage')); // 设置标题
        $this->controller->render('index', array());
    }
}