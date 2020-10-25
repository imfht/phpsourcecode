<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-23 14:24
 */
namespace Notadd\Mall\Handlers\Seller\Store\Dynamic;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreDynamic;

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
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::exists('mall_shop_dynamics'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺动态信息',
            'id.numeric'  => '店铺动态 ID 必须为数值',
            'id.required' => '店铺动态 ID 必须填写',
        ]);
        $this->beginTransaction();
        $dynamic = StoreDynamic::query()->onlyTrashed()->find($this->request->input('id'));
        if ($dynamic instanceof StoreDynamic && $dynamic->restore()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除店铺动态成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的店铺动态！');
        }
    }
}
