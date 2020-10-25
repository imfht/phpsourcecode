<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-25 16:35
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Notadd\Foundation\Member\Member;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class Order.
 */
class Order extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'address_id',
        'flow_marketing',
        'status',
        'store_id',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_orders';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'mall.order';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'launch',    // 发起订单
            'launched',  // 发起完成
            'pay',       // 等待支付
            'payed',     // 支付完成
            'deliver',   // 等待发货
            'delivered', // 发货完成
            'take',      // 等待收货
            'took',      // 收货完成
            'cancel',    // 取消
            'cancelled', // 已取消
        ];
    }

    /**
     * Definition of transitions for flow.
     *
     * @return array
     */
    public function transitions()
    {
        return [
            new Transition('cancel', 'cancel', 'cancelled'),
            new Transition('deliver', 'deliver', 'delivered'),
            new Transition('launch', 'launch', 'launched'),
            new Transition('pay', 'pay', 'payed'),
            new Transition('take', 'take', 'took'),
            new Transition('need_to_cancel', ['launched', 'payed', 'delivered'], 'cancel'),
            new Transition('wait_to_deliver', 'payed', 'deliver'),
            new Transition('wait_to_pay', 'launched', 'pay'),
            new Transition('wait_to_take', 'delivered', 'take'),
        ];
    }

    /**
     * Guard a transition.
     *
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     */
    public function guardTransition(GuardEvent $event)
    {
        switch ($event->getTransition()->getName()) {
            case 'cancel':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'deliver':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'launch':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'pay':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'take':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_cancel':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_deliver':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_pay':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_take':
                $this->blockTransition($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
