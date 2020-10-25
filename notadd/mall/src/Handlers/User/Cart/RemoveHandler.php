<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 13:35
 */
namespace Notadd\Mall\Handlers\User\Cart;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\UserCart;

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
                Rule::exists('mall_user_carts'),
                Rule::numeric(),
                Rule::required(),
            ],
        ], [
            'id.exists'   => '没有对应的购物车信息',
            'id.numeric'  => '购物车 ID 必须为数值',
            'id.required' => '购物车 ID 必须填写',
        ]);
        $this->beginTransaction();
        $cart = UserCart::query()->find($this->request->input('id'));
        if ($cart instanceof UserCart && $cart->delete()) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('删除购物车新词信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('没有对应的购物车信息！');
        }
    }
}
