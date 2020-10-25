<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-22 18:12
 */
namespace Notadd\Mall\Handlers\Administration\Statistics\Store;

use Illuminate\Support\Collection;
use Notadd\Foundation\Routing\Abstracts\Handler;

/**
 * Class AreaHandler.
 */
class AreaHandler extends Handler
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
