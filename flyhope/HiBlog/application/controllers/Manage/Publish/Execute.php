<?php
/**
 * 执行发布任务
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */
class Manage_Publish_ExecuteController extends AbsController {
    
    public function indexAction() {
        $result = Model\Publish\Task::execute();
        $this->viewDisplay(array(
            'result' => $result,
        ));
    }
}
