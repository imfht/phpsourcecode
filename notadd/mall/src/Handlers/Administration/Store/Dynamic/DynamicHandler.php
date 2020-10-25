<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 12:20
 */
namespace Notadd\Mall\Handlers\Administration\Store\Dynamic;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreDynamic;

/**
 * Class DynamicHandler.
 */
class DynamicHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.numeric' => '动态 ID 必须为数值',
            'id.required' => '动态 ID 必须填写',
        ]);
        $dynamic = StoreDynamic::query()->find($this->request->input('id'));
        if ($dynamic instanceof StoreDynamic) {
            $this->withCode(200)->withData($dynamic)->withMessage('获取店铺动态信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的店铺动态信息！');
        }
    }
}
