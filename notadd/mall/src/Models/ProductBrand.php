<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 18:25
 */
namespace Notadd\Mall\Models;

use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Symfony\Component\Workflow\Event\GuardEvent;

/**
 * Class ProductBrand.
 */
class ProductBrand extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
        'flow_marketing',
        'initial',
        'logo',
        'name',
        'order',
        'recommend',
        'show',
        'store_id',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'order'     => 'null|0',
        'recommend' => 'null|0',
        'store_id'  => 'null|0',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_product_brands';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

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
