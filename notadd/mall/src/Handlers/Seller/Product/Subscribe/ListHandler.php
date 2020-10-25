<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 19:55
 */
namespace Notadd\Mall\Handlers\Seller\Product\Subscribe;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\ProductSubscribe;

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
            'order'    => Rule::in([
                'asc',
                'desc',
            ]),
            'page'     => Rule::numeric(),
            'paginate' => Rule::numeric(),
            'store_id' => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'order.in'          => '排序规则错误',
            'page.numeric'      => '当前页面必须为数值',
            'paginate.numeric'  => '分页数必须为数值',
            'store_id.exists'   => '没有对应的店铺分类信息',
            'store_id.numeric'  => '店铺 ID 必须为数值',
            'store_id.required' => '店铺 ID 必须填写',
        ]);
        $builder = ProductSubscribe::query();
        $builder->orderBy('created_at', $this->request->input('order', 'desc'));
        $builder->where('store_id', $this->request->input('store_id'));
        $builder = $builder->paginate($this->request->input('paginate', 20));
        $this->withCode(200)->withData($builder->items())->withExtra([
            'pagination' => [
                'total'         => $builder->total(),
                'per_page'      => $builder->perPage(),
                'current_page'  => $builder->currentPage(),
                'last_page'     => $builder->lastPage(),
                'next_page_url' => $builder->nextPageUrl(),
                'prev_page_url' => $builder->previousPageUrl(),
                'from'          => $builder->firstItem(),
                'to'            => $builder->lastItem(),
            ],
        ])->withMessage('获取商品列表成功！');
    }
}
