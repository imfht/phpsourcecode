<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 16:15
 */
namespace Notadd\Mall\Handlers\Administration\Store;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Store;

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
            'status'   => Rule::in([
                'review',
                'opening',
                'closed',
                'banned',
            ]),
        ], [
            'order.in'         => '排序规则错误',
            'page.numeric'     => '当前页面必须为数值',
            'paginate.numeric' => '分页数必须为数值',
            'status.in'        => '店铺状态参数错误，必须为：review, opening, closed, banned',
        ]);
        $builder = Store::query();
        $this->request->has('status') && $builder->where('status', $this->request->input('status'));
        $builder->orderBy('created_at', $this->request->input('order', 'desc'));
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
        ])->withMessage('获取店铺列表成功！');
    }
}
