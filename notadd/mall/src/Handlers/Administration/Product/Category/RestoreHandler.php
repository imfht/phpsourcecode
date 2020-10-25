<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-26 14:55
 */
namespace Notadd\Mall\Handlers\Administration\Product\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductCategory;

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
            'id.numeric'  => '分类 ID 必须为数值',
            'id.required' => '分类 ID 必须填写',
        ]);
        $this->beginTransaction();
        $category = ProductCategory::query()->onlyTrashed()->find($this->request->input('id'));
        if ($category instanceof ProductCategory && $category->restore()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('恢复分类数据成功！');
        } else {
            $this->withCode(500)->withError('恢复分类数据失败！');
        }
    }
}
