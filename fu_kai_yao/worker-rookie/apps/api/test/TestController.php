<?php
namespace apps\api\test;

use apps\api\BaseController;
use system\services\SrvType;
use workerbase\classs\AttachEvent;
use workerbase\classs\datalevels\RdbTransaction;
use workerbase\classs\ServiceFactory;
use workerbase\traits\Response;
use workerbase\traits\Tools;

class TestController extends BaseController
{
    use Tools,Response;

    //前置操作
    protected function beforeAction($path)
    {
        return true;
    }

    /**
     * 启用过滤器
     * @return array
     */
    public function filters()
    {
        return [
            '\system\commons\base\filters\FrequencyFilter'
        ];
    }
  
    public function test()
    {
        $testSrv = ServiceFactory::getService(SrvType::COMMON_TEST);
        $data = $testSrv->test('good');
        return $this->showResponse(200, $data);
    }

    public function test2()
    {
        $testSrv = ServiceFactory::getService(SrvType::COMMON_TEST);
        $data = $testSrv->getInfoById(1);
        return $this->showResponse(200, 'ok',$data);
    }

    public function test3()
    {
        $testSrv = ServiceFactory::getService(SrvType::COMMON_TEST);
        RdbTransaction::getInstance()->begin();
        $id = $testSrv->add(['test'=>date('Y-m-d H:i:s')]);

        RdbTransaction::getInstance()->commit();

        return $this->showResponse(200, 'ok', $id);
    }
}