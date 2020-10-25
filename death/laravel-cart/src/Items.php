<?php
/**
 * 作者: Tanwen
 * 邮箱: 361657055@qq.com
 * 所在地: 广东广州
 * 时间: 2018/5/24 上午10:03
 */

namespace Tanwencn\Cart;


use Illuminate\Support\Collection;

class Items extends Collection
{
    public function subtotal()
    {
        return $this->sum('subtotal');
    }
}