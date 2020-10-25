<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 15:02
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class ShopRate.
 */
class StoreRate extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'flow_marketing',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_shop_rates';

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'mall.store.rate';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'rate',      // 评价
            'rated',     // 评价完胜
            'review',    // 审核
            'reviewed',  // 审核完成
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
            new Transition('rate', 'rate', 'rated'),
            new Transition('wait_to_review', 'rated', 'review'),
            new Transition('review', 'review', 'reviewed'),
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
            case 'rate':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'wait_to_review':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'review':
                $this->blockTransition($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
