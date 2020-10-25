<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 17:17
 */
namespace Notadd\Mall\Handlers\Seller\Store\Dynamic;

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
            'content'  => Rule::required(),
            'id'       => [
                Rule::exists('mall_shop_dynamics'),
                Rule::numeric(),
                Rule::required(),
            ],
            'show'     => [
                Rule::numeric(),
                Rule::required(),
            ],
            'store_id' => [
                Rule::exists('mall_stores'),
                Rule::numeric(),
                Rule::required(),
            ],
            'title'    => Rule::required(),
        ], [
            'content.required'  => '动态内容必须填写',
            'id.exists'         => '没有对应的店铺动态信息',
            'id.numeric'        => '店铺动态 ID 必须为数值',
            'id.required'       => '店铺动态 ID 必须填写',
            'show.numeric'      => '是否显示必须为数值',
            'show.required'     => '是否显示必须填写',
            'store_id.exists'   => '没有对应的店铺信息',
            'store_id.required' => '店铺 ID 必须填写',
            'store_id.numeric'  => '店铺 ID 必须为数值',
            'title.required'    => '动态标题必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'content',
            'show',
            'store_id',
            'thumbnail',
            'title',
        ]);
        $dynamic = StoreDynamic::query()->find($this->request->input('id'));
        if ($dynamic instanceof StoreDynamic && $dynamic->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑店铺动态成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的店铺动态！');
        }
    }
}
