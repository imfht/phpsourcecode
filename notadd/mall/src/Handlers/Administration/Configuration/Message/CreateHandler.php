<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 15:35
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Message;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\Message;

/**
 * Class CreateHandler.
 */
class CreateHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->validate($this->request, [], []);
        $this->beginTransaction();
        $data = $this->request->only([]);
        if (Message::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('');
        }
    }
}
