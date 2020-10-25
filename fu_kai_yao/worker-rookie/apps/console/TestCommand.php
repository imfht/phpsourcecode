<?php
namespace apps\console;

use config\constants\WorkerTypes;
use system\services\SrvType;
use workerbase\classs\AttachEvent;
use workerbase\classs\datalevels\RdbTransaction;
use workerbase\classs\MQ\imps\MessageServer;
use workerbase\classs\ServiceFactory;
use Swoole\Coroutine as Co;


class TestCommand
{
    public function test()
    {
        $num = 3;
        while ($num--) {
            $res = MessageServer::getInstance()->dispatch(WorkerTypes::COMMON_TEST, ['hello good']);
//            if ($res) {
//                var_dump('worker队列发送成功1');
//            }
        }

        return true;
    }

    public function test2()
    {
        $num = 10;
        while ($num--) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                die('fork false'.PHP_EOL);
            } elseif($pid == 0){
                $response = MessageServer::getInstance()->receive('redisMQ:test-Worker-defaultJob');
                if ($response) {
                    $res = MessageServer::getInstance()->delete('redisMQ:test-Worker-defaultJob', $response['msgBody']);
                    if (!$res) {
                        var_dump('删除2',$response);
                    }
                }
                die;
            }
        }
        while (pcntl_waitpid(0, $status) != -1) {
            pcntl_wexitstatus($status);
            echo "子进程完成" . PHP_EOL;
        }
        return true;
    }

    public function test3()
    {
        $testSrv = ServiceFactory::getService(SrvType::COMMON_TEST);

        RdbTransaction::getInstance()->begin();
        $id = $testSrv->add(['test'=>date('Y-m-d H:i:s')]);
        RdbTransaction::getInstance()->commit();
        return true;
    }
}