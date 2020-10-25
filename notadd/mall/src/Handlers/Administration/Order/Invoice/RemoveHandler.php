<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 18:04
 */
namespace Notadd\Mall\Handlers\Administration\Order\Invoice;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\OrderInvoice;

/**
 * Class RemoveHandler.
 */
class RemoveHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $id = $this->request->input('id');
        $invoice = OrderInvoice::query()->find($id);
        if ($invoice && $invoice->delete()) {
            $this->withCode(200)->withMessage('');
        } else {
            $this->withCode(500)->withError('');
        }
    }
}
