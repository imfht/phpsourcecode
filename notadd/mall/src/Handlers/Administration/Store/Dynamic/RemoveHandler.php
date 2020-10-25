<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 12:22
 */
namespace Notadd\Mall\Handlers\Administration\Store\Dynamic;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreDynamic;

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
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.numeric' => '动态 ID 必须为数值',
            'id.required' => '动态 ID 必须填写',
        ]);
        $this->beginTransaction();
        $dynamic = StoreDynamic::query()->find($this->request->input('id'));
        if ($dynamic instanceof StoreDynamic && $dynamic->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除店铺动态成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的店铺分类信息！');
        }
    }
}
