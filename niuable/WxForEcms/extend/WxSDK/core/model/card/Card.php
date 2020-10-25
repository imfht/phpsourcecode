<?php
namespace WxSDK\core\model\card;

use WxSDK\core\model\Model;

class Card extends Model{
    /**
     * @var string 卡券类型：GROUPON（团购），CASH（代金券），DISCOUNT（折扣券），GIFT（兑换券），GENERAL_COUPON（优惠券）
     */
    public $card_type;
    /**
     * @var GroupOn|null
     */
    public $groupon;

    /**
     * @var Cash|null
     */
    public $cash;
    /**
     * @var Discount|null
     */
    public $discount;
    /**
     * @var GeneralCoupon|null
     *
     */
    public $general_coupon;
    /**
     * @var Gift|null
     */
    public $gift;

    /**
     * Card constructor.
     * @param string $card_type 卡券类型：GROUPON（团购），CASH（代金券），DISCOUNT（折扣券），GIFT（兑换券），GENERAL_COUPON（优惠券）
     */
    public function __construct(string $card_type)
    {
        $this->card_type = $card_type;
    }
}

