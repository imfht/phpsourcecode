<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 11:59
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Advertisement;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Mall\Models\Advertisement;

/**
 * Class EditHandler.
 */
class EditHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $id = $this->request->input('id');
        $advertisement = Advertisement::query()->find($id);
        if ($advertisement) {
            $advertisement->update($this->request->all());
            $this->withCode(200)->withMessage('');
        } else {
            $this->withCode(500)->withError('');
        }
    }
}
