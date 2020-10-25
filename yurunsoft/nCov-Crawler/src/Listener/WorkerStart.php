<?php
namespace ImiApp\Listener;

use Imi\Event\EventParam;
use Yurun\Util\YurunHttp;
use Imi\Event\IEventListener;
use Imi\Bean\Annotation\Listener;

/**
 * @Listener("IMI.MAIN_SERVER.WORKER.START")
 * @Listener("IMI.PROCESS.BEGIN")
 */
class WorkerStart implements IEventListener
{
    /**
     * 事件处理方法
     * @param EventParam $e
     * @return void
     */
    public function handle(EventParam $e)
    {
        YurunHttp::setDefaultHandler(\Yurun\Util\YurunHttp\Handler\Swoole::class);
    }

}
