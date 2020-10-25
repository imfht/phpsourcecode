<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-28 17:48
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Symfony\Component\Workflow\Event\GuardEvent;

/**
 * Class Message.
 */
class Message extends Model
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
    protected $table = '';

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        // TODO: Implement name() method.
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        // TODO: Implement places() method.
    }

    /**
     * Definition of transitions for flow.
     *
     * @return array
     */
    public function transitions()
    {
        // TODO: Implement transitions() method.
    }

    /**
     * Guard a transition.
     *
     * @param \Symfony\Component\Workflow\Event\GuardEvent $event
     */
    public function guardTransition(GuardEvent $event)
    {
        // TODO: Implement guardTransition() method.
    }
}
