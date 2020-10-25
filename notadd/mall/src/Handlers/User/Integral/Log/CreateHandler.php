<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-05 14:53
 */
namespace Notadd\Mall\Handlers\User\Integral\Log;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserIntegralLog;

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
    protected function execute()
    {
        $this->validate($this->request, [
            'comment'  => Rule::required(),
            'integral' => [
                Rule::numeric(),
                Rule::required(),
            ],
            'user_id'  => [
                Rule::exists('mall_users'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'comment.required'  => '注释必填填写',
            'integral.required' => '积分必须填写',
            'integral.numeric'  => '积分必须为数值',
            'user_id.exists'    => '没有对应的用户信息',
            'user_id.numeric'   => '用户 ID 必须为数值',
            'user_id.required'  => '用户 ID 必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'comment',
            'integral',
            'user_id',
        ]);
        if (UserIntegralLog::query()->create($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建积分日志成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建积分日志失败！');
        }
    }
}
