<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 15:14
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Notadd\Foundation\Member\Member;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class OrderExpress.
 */
class OrderExpress extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'address_id',
        'courier_number',
        'express_company',
        'flow_marketing',
        'order_id',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_order_expresses';

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
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Member::class, 'user_id');
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'mall.order.express';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'send',
            'sent',
            'take',
            'took',
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
            new Transition('send', 'send', 'sent'),
            new Transition('wait_to_take', 'sent', 'take'),
            new Transition('take', 'take', 'took'),
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
            case 'send':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_take':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'take':
                $this->blockTransition($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
