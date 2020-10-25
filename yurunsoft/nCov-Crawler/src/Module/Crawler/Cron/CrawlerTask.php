<?php
namespace ImiApp\Module\Crawler\Cron;

use Imi\Log\Log;
use Imi\Cron\Annotation\Cron;
use Imi\Aop\Annotation\Inject;
use Imi\Cron\Contract\ICronTask;

/**
 * 目前暂定 3 分钟采集一次
 * @Cron(id="CrawlerTask", type="task", minute="3n")
 */
class CrawlerTask implements ICronTask
{
    /**
     * @Inject("CrawlerService")
     *
     * @var \ImiApp\Module\Crawler\Service\CrawlerService
     */
    protected $crawlerService;

    /**
     * 执行任务
     *
     * @param string $id
     * @param mixed $data
     * @return void
     */
    public function run(string $id, $data)
    {
        $this->crawlerService->run();
    }

}
