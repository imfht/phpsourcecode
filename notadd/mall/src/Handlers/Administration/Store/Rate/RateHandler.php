<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 16:45
 */
namespace Notadd\Mall\Handlers\Administration\Store\Rate;

use Notadd\Foundation\Routing\Abstracts\Handler;

/**
 * Class RateHandler.
 */
class RateHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $id = $this->request->input('id');
    }
}
