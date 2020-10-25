<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-05 20:43
 */
namespace Notadd\Mall\Handlers\Administration\Product\Specification;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductSpecification;

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
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.numeric'  => '规格 ID 必须为数值',
            'id.required' => '规格 ID 必须填写',
        ]);
        $this->beginTransaction();
        $specification = ProductSpecification::query()->find($this->request->input('id'));
        if ($specification instanceof ProductSpecification && $specification->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除商品规格成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的商品规格！');
        }
    }
}
