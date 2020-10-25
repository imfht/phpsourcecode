<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 15:28
 */
namespace Notadd\Mall\Handlers\User\Integral;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserIntegral;

/**
 * Class IntegralHandler.
 */
class IntegralHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'user_id' => [
                Rule::exists('mall_users'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'user_id.exists'   => '没有对应的用户信息',
            'user_id.numeric'  => '用户 ID 必须为数值',
            'user_id.required' => '用户 ID 必须填写',
        ]);
        $integral = UserIntegral::query()->where('user', $this->request->input('user_id'))->first();
        if ($integral instanceof UserIntegral) {
            $this->withCode(200)->withData($integral)->withMessage('获取用户积分信息成功！');
        } else {
            $this->withCode(500)->withError('没有对应的用户积分信息！');
        }
    }
}
