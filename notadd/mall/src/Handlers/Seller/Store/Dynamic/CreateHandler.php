<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 17:16
 */
namespace Notadd\Mall\Handlers\Seller\Store\Dynamic;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreDynamic;

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
            'content'  => Rule::required(),
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
        if (StoreDynamic::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建店铺动态成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建店铺动态失败！');
        }
    }
}
