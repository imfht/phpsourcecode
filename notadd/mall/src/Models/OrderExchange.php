<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 14:58
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Notadd\Foundation\Member\Member;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class OrderExchange.
 */
class OrderExchange extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'amount',
        'address_for_take',
        'address_for_exchange',
        'express_id_for_receive',
        'express_id_for_exchange',
        'flow_marketing',
        'order_id',
        'reason',
        'remark',
        'response',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_order_exchanges';

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
        return 'mall.order.exchange';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'launch',
            'launched',
            'deliver',
            'delivered',
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
            new Transition('launch', 'launch', 'launched'),
            new Transition('wait_to_deliver', 'launched', 'deliver'),
            new Transition('deliver', 'deliver', 'delivered'),
            new Transition('wait_to_send', 'delivered', 'send'),
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
            case 'launch':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_deliver':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'deliver':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_send':
                $this->blockTransition($event, $this->permission(''));
                break;
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
