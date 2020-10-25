<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-01 00:22
 */

namespace Notadd\Mall\Handlers\Store\Product\Category;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductCategory;

/**
 * Class ListHandler.
 */
class ListHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'order'     => Rule::in([
                'asc',
                'desc',
            ]),
            'page'      => Rule::numeric(),
            'paginate'  => Rule::numeric(),
            'parent_id' => Rule::numeric(),
        ], [
            'order.in'          => '排序规则错误',
            'page.numeric'      => '当前页面必须为数值',
            'paginate.numeric'  => '分页数必须为数值',
            'parent_id.numeric' => '父级分类 ID 必须为数值',
        ]);
        $parent_id = $this->request->input('parent_id', 0);
        $builder = ProductCategory::query();
        $builder->with('children.children.children');
        $builder->where('parent_id', $parent_id);
        $builder->orderBy('created_at', $this->request->input('order', 'desc'));
        $this->withCode(200)->withData($builder->get())->withMessage('获取产品分类信息成功！');
    }
}
