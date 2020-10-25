<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 * @author seiven-com-pc\user
 */
class IndexController extends Yaf_Controller_Abstract {
    
    // 从2.1开始, errorAction支持直接通过参数获取异常
    public function IndexAction($name = 'Index'){
        // 1. fetch query
        $get = $this->getRequest()->getQuery("get", "default value");
        // 2. fetch model
        $model = new SampleModel();
        
        // 3. assign
        $this->getView()->assign("content", $model->selectSample());
        $this->getView()->assign("name", $name);
    }
}
