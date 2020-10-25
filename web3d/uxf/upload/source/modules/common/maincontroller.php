<?php
/**
 * 类名没有下划线分割
 */
class MainController extends Mvc_Controller {
    /**
     * 默认首页
     */
    public function actIndex(){
        $this->import('model/some');
        $model = new Model_Some();

        $msg = $model->foo();

        $this->setVar('msg', $msg);
        $this->setVar('ticket_id', 134);
        $this->display('index');
    }
}
