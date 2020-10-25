<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 11:47
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Search\Hot;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;

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
    }
}
