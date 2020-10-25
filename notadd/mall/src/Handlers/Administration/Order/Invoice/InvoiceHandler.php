<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:59
 */
namespace Notadd\Mall\Handlers\Administration\Order\Invoice;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\OrderInvoice;

/**
 * Class InvoiceHandler.
 */
class InvoiceHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $id = $this->request->input('id');
        if (OrderInvoice::query()->where('id', $id)->count()) {
            $this->withCode(200)->withData(OrderInvoice::query()->find($id))->withMessage('');
        } else {
            $this->withCode(500)->withError('');
        }
    }
}
