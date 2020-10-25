<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 16:48
 */
namespace Notadd\Mall\Handlers\User\Collection;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserCollection;

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
            'user_id'  => [
                Rule::exists('mall_users'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'order.in'         => '排序规则错误',
            'page.numeric'     => '当前页面必须为数值',
            'paginate.numeric' => '分页数必须为数值',
            'user_id.exists'   => '没有对应的用户信息',
            'user_id.numeric'  => '用户 ID 必须为数值',
            'user_id.required' => '用户 ID 必须填写',
        ]);
        $builder = UserCollection::query();
        $builder->where('user_id', $this->request->input('id'));
        $builder->orderBy('created_at', $this->request->input('order', 'desc'));
        $builder = $builder->paginate($this->request->input('paginate', 20));
        $this->withCode(200)->withData($builder->items())->withMessage('获取订单列表成功！')->withExtra([
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
        ]);
    }
}
