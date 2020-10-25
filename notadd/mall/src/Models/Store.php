<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 15:08
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class Shop.
 */
class Store extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'address',
        'category_id',
        'company',
        'end_at',
        'flow_marketing',
        'grade',
        'location',
        'name',
        'open_at',
        'status',
        'user_id',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'category_id' => 'null|0',
        'level'       => 'null|0',
        'status'      => 'null|review',
        'user_id'     => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_stores';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function information()
    {
        return $this->belongsTo(StoreInformation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function navigations()
    {
        return $this->hasMany(StoreNavigation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function outlets()
    {
        return $this->hasMany(StoreOutlet::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suppliers()
    {
        return $this->hasMany(StoreSupplier::class);
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'mall.store';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'register',
            'registered',
            'close',
            'closed',
            'open',
            'opened',
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
            new Transition('register', 'register', 'registered'),
            new Transition('need_to_close', [
                'opened',
                'registered',
            ], 'close'),
            new Transition('close', 'close', 'closed'),
            new Transition('need_to_open', 'registered', 'open'),
            new Transition('open', 'open', 'opened'),
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
            case 'register':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_close':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'close':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_open':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'open':
                $this->blockTransition($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
