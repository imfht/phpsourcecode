<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-30 14:24
 */
namespace Notadd\Mall\Handlers\User\Footprint;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserFootprint;

/**
 * Class FootprintHandler.
 */
class FootprintHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'id' => [
                Rule::exists('mall_user_footprints'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的足迹信息',
            'id.numeric'  => '足迹 ID 必须为数值',
            'id.required' => '足迹 ID 必须填写',
        ]);
        $footprint = UserFootprint::query()->find($this->request->input('id'));
        if ($footprint instanceof UserFootprint) {
            $this->withCode(200)->withData($footprint)->withMessage('获取足迹信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的足迹信息！');
        }
    }
}
