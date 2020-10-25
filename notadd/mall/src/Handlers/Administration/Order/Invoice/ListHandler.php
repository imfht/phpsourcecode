<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:54
 */
namespace Notadd\Mall\Handlers\Administration\Order\Invoice;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\OrderInvoice;

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
        $builder = OrderInvoice::query();
        $this->withCode(200)->withData($builder->get())->withMessage('');
    }
}
