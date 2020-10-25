<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 12:13
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\AdvertisementPosition;

/**
 * Class ListHandler.
 */
class ListHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $builder = AdvertisementPosition::query();
        $this->withCode(200)->withData($builder->get())->withMessage('');
    }
}
