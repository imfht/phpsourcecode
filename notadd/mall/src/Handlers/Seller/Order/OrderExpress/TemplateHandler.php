<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 16:59
 */
namespace Notadd\Mall\Handlers\Seller\Order\OrderExpress;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Store;

/**
 * Class TemplateHandler.
 */
class TemplateHandler extends Handler
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
                Rule::exists(''),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺信息',
            'id.numeric'  => '店铺 ID 必须为数值',
            'id.required' => '店铺 ID 必须填写',
        ]);
        $store = Store::query()->find($this->request->input('id'));
        if ($store instanceof Store) {
            $this->withCode(200)->withMessage('更新店铺物流模板配置成功！');
        } else {
            $this->withCode(500)->withError('更新店铺物流模板配置失败！');
        }
    }
}
