<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 14:57
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Notadd\Foundation\Member\Member;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class OrderRefund.
 */
class OrderRefund extends Model
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
        'pay',
        'reason',
        'remark',
        'response',
        'user_id',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_order_refunds';

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
        return 'mall.refund';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'launch',      // 发起退款
            'launched',    // 发起完成
            'review',      // 审核退款
            'reviewed',    // 审核完成
            'reject',      // 拒绝
            'rejected',    // 拒绝完成
            'refund',      // 退货
            'refunded',    // 退货完成
            'reimburse',   // 退款
            'reimbursed',  // 退款完成
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
            new Transition('wait_to_review', 'launched', 'review'),
            new Transition('review', 'review', 'review'),
            new Transition('wait_to_refund', 'review', 'refund'),
            new Transition('need_to_reject', 'review', 'reject'),
            new Transition('reject', 'reject', 'rejected'),
            new Transition('refund', 'refund', 'refund'),
            new Transition('wait_to_reimburse', 'refund', 'reimburse'),
            new Transition('reimburse', 'reimburse', 'reimburse'),
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
            case 'wait_to_review':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'review':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_refund':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_reject':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'reject':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'refund':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_reimburse':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'reimburse':
                $this->blockTransition($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
