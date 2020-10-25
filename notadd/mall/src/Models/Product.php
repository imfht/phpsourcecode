<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-24 17:48
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class Product.
 */
class Product extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'barcode',
        'brand_id',
        'business_item',
        'category_id',
        'description',
        'flow_marketing',
        'inventory',
        'inventory_warning',
        'library_id',
        'name',
        'price',
        'price_cost',
        'price_market',
        'store_id',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'brand_id'          => 'null|0',
        'inventory'         => 'null|0',
        'inventory_warning' => 'null|0',
        'library_id'        => 'null|0',
        'price'             => 'null|0',
        'price_cost'        => 'null|0',
        'price_market'      => 'null|0',
        'store_id'          => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_products';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(ProductBrand::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pictures()
    {
        return $this->hasMany(ProductPicture::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates()
    {
        return $this->hasMany(ProductRate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribes()
    {
        return $this->hasMany(ProductSubscribe::class);
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'mall.product';
    }

    /**
     * Definition of places for flow.
     *
     * @return array
     */
    public function places()
    {
        return [
            'create',
            'created',
            'edit',
            'edited',
            'remove',
            'removed',
            'publish',
            'published',
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
            new Transition('create', 'create', 'created'),
            new Transition('need_to_edit', 'created', 'edit'),
            new Transition('edit', 'edit', 'edited'),
            new Transition('need_to_remove', [
                'created',
                'edited',
            ], 'remove'),
            new Transition('remove', 'remove', 'removed'),
            new Transition('need_to_publish', [
                'created',
                'edited',
            ], 'publish'),
            new Transition('publish', 'publish', 'published'),
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
            case 'create':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_edit':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'edit':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_remove':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'remove':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'need_to_publish':
                $this->blockTransition($event, $this->permission(''));
                break;
            case 'publish':
                $this->blockTransition($event, $this->permission(''));
                break;
            default:
                $event->setBlocked(true);
        }
    }
}
