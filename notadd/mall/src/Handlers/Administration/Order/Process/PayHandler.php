<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:10
 */
namespace Notadd\Mall\Handlers\Administration\Order\Process;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\OrderProcess;

/**
 * Class PayHandler.
 */
class PayHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $id = $this->request->input('id');
        $process = OrderProcess::query()->find($id);
        if ($process) {
            $this->withCode(200)->withMessage('');
        } else {
            $this->withCode(500)->withError('');
        }
    }
}
