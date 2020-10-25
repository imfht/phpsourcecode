<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-22 16:25
 */
namespace Notadd\Mall\Handlers\Administration\Statistics;

use Illuminate\Support\Collection;
use Notadd\Foundation\Routing\Abstracts\Handler;

/**
 * Class GetHandler.
 */
class GetHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $data = new Collection();
        $this->withCode(200)->withData($data)->withMessage('获取统计数据成功！');
    }
}
