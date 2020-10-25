<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 15:48
 */
namespace Notadd\Mall\Models;

use Illuminate\Support\Collection;
use Notadd\Foundation\Database\Model;
use Notadd\Foundation\Database\Traits\HasFlow;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Transition;

/**
 * Class ProductCategory.
 */
class ProductCategory extends Model
{
    use HasFlow;

    /**
     * @var array
     */
    protected $appends = [
        'breadcrumb',
        'level',
//        'path',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'deposit',
        'flow_marketing',
        'logo',
        'name',
        'order',
        'parent_id',
        'show',
    ];

    /**
     * @var array
     */
    protected $setters = [
        'deposit'   => 'null|0',
        'order'     => 'null|0',
        'parent_id' => 'null|0',
        'show'      => 'null|spu',
    ];

    /**
     * @var string
     */
    protected $table = 'mall_product_categories';

    /**
     * @param $value
     *
     * @return string
     */
    public function getBreadcrumbAttribute($value)
    {
        $paths = new Collection([$this]);
        if ($this->attributes['parent_id'] && ($one = static::query()->find($this->attributes['parent_id'])) instanceof ProductCategory) {
            $paths->prepend($one);
            if ($one->getAttribute('parent_id') && ($two = static::query()->find($one->getAttribute('parent_id'))) instanceof ProductCategory) {
                $paths->prepend($two);
            }
        }
        $paths->transform(function (ProductCategory $category) {
            return $category->getAttribute('name');
        });

        return $paths->implode(' / ');
    }

    /**
     * @param $value
     *
     * @return int
     */
    public function getLevelAttribute($value)
    {
        if (static::query()->where('id', $this->attributes['parent_id'])->count()) {
            $parent = static::query()->find($this->attributes['parent_id']);
            if (static::query()->where('id', $parent->getAttribute('parent_id'))->count()) {
                return 3;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getOrderAttribute($value)
    {
        if (is_null($value)) {
            return 0;
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getPathAttribute($value)
    {
        $paths = new Collection();
        if ($this->attributes['parent_id']) {
            $one = static::query()->find($this->attributes['parent_id']);
            $paths->prepend($one->getAttribute('id'));
            if ($one->getAttribute('parent_id')) {
                $two = static::query()->find($one->getAttribute('parent_id'));
                $paths->prepend($two->getAttribute('id'));
            }
        }

        return $paths->toArray();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Definition of name for flow.
     *
     * @return string
     */
    public function name()
    {
        return 'mall.product.category';
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
            default:
                $event->setBlocked(true);
        }
    }
}
