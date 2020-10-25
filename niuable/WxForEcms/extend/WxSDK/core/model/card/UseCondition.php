<?php


namespace WxSDK\core\model\card;


use WxSDK\core\model\Model;

class UseCondition extends Model
{
    /**
     * @var string 指定可用的商品类目，仅用于代金券类型 ，填入后将在券面拼写适用于xxx
     */
    public $accept_category;
    /**
     * @var string    指定不可用的商品类目，仅用于代金券类型 ，填入后将在券面拼写不适用于xxxx
     */
    public $reject_category;
    /**
     * @var int    满减门槛字段，可用于兑换券和代金券 ，填入后将在页面拼写消费满xx元可用。
     */
    public $least_cost;
    /**
     * @var string 购买xx可用类型门槛，仅用于兑换 ，填入后自动拼写购买xxx可用。
     */
    public $object_use_for;
    /**
     * @var bool 是否可以与其他类型共享门槛：
     * 填写false时系统将在使用须知里 拼写“不可与其他优惠共享”；
     * 填写true时系统将在使用须知里 拼写“可与其他优惠共享”；
     * 默认为true
     */
    public $can_use_with_other_discount = true;

}