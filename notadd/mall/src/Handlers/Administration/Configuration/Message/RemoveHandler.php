<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 15:43
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Message;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Message;

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
        $this->validate($this->request, [
            'id' => [
                Rule::exists(''),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '',
            'id.numeric'  => '',
            'id.required' => '',
        ]);
        $this->beginTransaction();
        $message = Message::query()->find($this->request->input('id'));
        if ($message && $message->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('');
        }
    }
}
