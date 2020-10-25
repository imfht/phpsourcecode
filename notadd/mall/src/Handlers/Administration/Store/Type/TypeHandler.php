<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-27 19:25
 */
namespace Notadd\Mall\Handlers\Administration\Store\Type;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreType;

/**
 * Class TypeHandler.
 */
class TypeHandler extends Handler
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
                Rule::exists('mall_store_types'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的店铺类型信息',
            'id.numeric'  => '店铺类型 ID 必须为数值',
            'id.required' => '店铺类型 ID 必须填写',
        ]);
        $grade = StoreType::query()->find($this->request->input('id'));
        if ($grade instanceof StoreType) {
            $this->withCode(200)->withData($grade)->withMessage('获取店铺类型信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的店铺类型信息！');
        }
    }
}
