<?php

namespace app\home\controller;

/**
 * Description of Widget
 * 侧边栏组件
 * @author static7
 */
class Widget extends Static7 {

    /**
     * 网站公告
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function notice() {
        $value = [
            'name' => 'thinkphp',
        ];
        return $this->view->assign($value)->fetch('Widget:notice');
    }

}
