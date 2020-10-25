<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:42
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Express;

use Notadd\Foundation\Routing\Abstracts\Handler;

/**
 * Class SetHandler.
 */
class SetHandler extends Handler
{
    /**
     * Execute Handler.
     */
    public function execute()
    {
        $this->withCode(200)->withMessage('');
    }
}
