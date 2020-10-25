<?php
namespace ImiApp\Module\Crawler\Tool;

use Imi\App;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\Annotation\Operation;

/**
 * @Tool("crawler")
 */
class Crawler
{
    /**
     * @Operation("run")
     *
     * @return void
     */
    public function run()
    {
        \Imi\Bean\Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
        /** @var \ImiApp\Module\Crawler\Service\CrawlerService $crawlerService */
        $crawlerService = App::getBean('CrawlerService');
        $crawlerService->run();
    }

}
