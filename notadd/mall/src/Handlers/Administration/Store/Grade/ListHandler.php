<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-15 19:44
 */
namespace Notadd\Mall\Handlers\Administration\Store\Grade;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreGrade;

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
        ], [
            'order.in'         => '排序规则错误',
            'page.numeric'     => '当前页面必须为数值',
            'paginate.numeric' => '分页数必须为数值',
        ]);
        $builder = StoreGrade::query();
        $builder->orderBy('created_at', $this->request->input('order', 'desc'));
        $items = $builder->get();
        $this->withCode(200)->withData($items)->withMessage('获取店铺等级列表成功！');
    }
}
