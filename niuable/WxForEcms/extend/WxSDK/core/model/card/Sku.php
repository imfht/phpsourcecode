<?php


namespace WxSDK\core\model\card;


use WxSDK\core\model\Model;

class Sku extends Model
{
    /**
     * @var int 必须，卡券库存的数量，上限为100000000。
     */
    public $quantity;

    /**
     * Sku constructor.
     * @param int $quantity
     */
    public function __construct(int $quantity)
    {
        $this->quantity = $quantity;
    }

}