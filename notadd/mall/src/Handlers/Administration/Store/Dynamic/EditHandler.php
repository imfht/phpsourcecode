<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 12:19
 */
namespace Notadd\Mall\Handlers\Administration\Store\Dynamic;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreDynamic;

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
            'deposit'   => [
                Rule::numeric(),
                Rule::required(),
            ],
            'id'        => [
                Rule::numeric(),
                Rule::required(),
            ],
            'name'      => Rule::required(),
            'parent_id' => Rule::numeric(),
            'order'     => Rule::numeric(),
        ], [
            'deposit.numeric'   => '保证金数额必须为数值',
            'deposit.required'  => '保证金数额必须填写',
            'id.numeric'        => '动态 ID 必须为数值',
            'id.required'       => '动态 ID 必须填写',
            'name.required'     => '分类名称必须填写',
            'parent_id.numeric' => '父级分类 ID 必须为数值',
            'order.numeric'     => '排序必须为数值',
        ]);
        $this->beginTransaction();
        $dynamic = StoreDynamic::query()->find($this->request->input('id'));
        $data = $this->request->only([
            'content',
            'show',
            'store_id',
            'thumbnail',
            'title',
        ]);
        if ($dynamic instanceof StoreDynamic && $dynamic->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑店铺动态成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的店铺分类信息！');
        }
    }
}
