<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-05 20:41
 */
namespace Notadd\Mall\Handlers\Administration\Product\Specification;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductSpecification;

/**
 * Class CreateHandler.
 */
class CreateHandler extends Handler
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
            'name'        => Rule::required(),
        ], [
            'category_id.numeric'  => '分类 ID 必须为数值',
            'category_id.required' => '分类 ID 必须填写',
            'name.required'        => '规格显示名称必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'category_id',
            'name',
            'type',
            'value',
        ]);
        if (ProductSpecification::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('添加商品规格成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('添加商品规格失败！');
        }
    }
}
