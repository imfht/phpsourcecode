<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-05 14:57
 */
namespace Notadd\Mall\Handlers\User\Integral\Log;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserIntegralLog;

/**
 * Class LogHandler.
 */
class LogHandler extends Handler
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
                Rule::exists('mall_user_integral_logs'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的用户积分日志信息',
            'id.numeric'  => '积分日志 ID 必须为数值',
            'id.required' => '积分日志 ID 必须填写',
        ]);
        $log = UserIntegralLog::query()->find($this->request->input('id'));
        if ($log instanceof UserIntegralLog) {
            $this->withCode(200)->withData($log)->withMessage('获取用户积分日志信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的用户积分日志信息！');
        }
    }
}
