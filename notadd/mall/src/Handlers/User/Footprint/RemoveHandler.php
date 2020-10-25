<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 17:41
 */
namespace Notadd\Mall\Handlers\User\Footprint;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserFootprint;

/**
 * Class RemoveHandler.
 */
class RemoveHandler extends Handler
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
                Rule::exists('mall_user_collections'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的足迹信息',
            'id.numeric'  => '足迹 ID 必须为数值',
            'id.required' => '足迹 ID 必须填写',
        ]);
        $this->beginTransaction();
        $footprint = UserFootprint::query()->find($this->request->input('id'));
        if ($footprint instanceof UserFootprint && $footprint->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除足迹成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的足迹信息！');
        }
    }
}
