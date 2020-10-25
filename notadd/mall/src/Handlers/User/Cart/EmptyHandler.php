<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 13:40
 */
namespace Notadd\Mall\Handlers\User\Cart;

use Illuminate\Database\Eloquent\Collection;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserCart;

/**
 * Class EmptyHandler.
 */
class EmptyHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
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
        $this->beginTransaction();
        $cart = UserCart::query()->where('user_id', $this->request->input('user_id'))->get();
        if ($cart instanceof Collection && $cart->every(function (UserCart $record) {
                return $record->delete();
            })
        ) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('清空购物车成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('清空购物车失败！');
        }
    }
}
