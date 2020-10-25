<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-05 20:42
 */
namespace Notadd\Mall\Handlers\Administration\Product\Specification;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductSpecification;

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
        $this->validate($this->request, [
            'category_id' => [
                Rule::numeric(),
                Rule::required(),
            ],
            'id'          => Rule::required(),
            'name'        => Rule::required(),
        ], [
            'category_id.numeric'  => '分类 ID 必须为数值',
            'category_id.required' => '分类 ID 必须填写',
            'id.numeric'           => '规格 ID 必须为数值',
            'id.required'          => '规格 ID 必须填写',
            'name.required'        => '规格显示名称必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'category_id',
            'name',
            'type',
            'value',
        ]);
        $specification = ProductSpecification::query()->find($this->request->input('id'));
        if ($specification instanceof ProductSpecification && $specification->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑商品规格成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withMessage('没有对应的商品规格！');
        }
    }
}
