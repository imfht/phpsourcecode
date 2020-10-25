<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 16:49
 */
namespace Notadd\Mall\Handlers\Administration\Store;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Store;

/**
 * Class RestoreHandler.
 */
class RestoreHandler extends Handler
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
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.numeric'  => '店铺 ID 必须填写',
            'id.required' => '店铺 ID 必须为数值',
        ]);
        $this->beginTransaction();
        $store = Store::query()->onlyTrashed()->find($this->request->input('id'));
        if ($store instanceof Store && $store->restore()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('恢复店铺数据成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的店铺信息！');
        }
    }
}
