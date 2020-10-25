<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午3:43.
 */

namespace MiotApi\Contract\Specification;

/**
 * 服务是一个独立的有意义的功能组，描述一个服务，需要说清楚：
 * 是什么服务？
 * 有什么方法可以操作？
 * 有什么事件可能会发生？
 * 有哪些属性？
 *
 * Class ServiceSpecification
 */
class ServiceSpecification extends Specification
{
    /**
     * 必选方法列表.
     *
     * @var
     */
    protected $requiredActions;

    /**
     * 可选方法列表.
     *
     * @var
     */
    protected $optionalActions;

    /**
     * 必选事件列表.
     *
     * @var
     */
    protected $requiredEvents;

    /**
     * 可选事件列表.
     *
     * @var
     */
    protected $optionalEvents;

    /**
     * 必选属性列表.
     *
     * @var
     */
    protected $requiredProperties;

    /**
     * 可选属性列表.
     *
     * @var
     */
    protected $optionalProperties;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function init()
    {
        parent::init();

        if ($this->has('required-actions')) {
            $requiredActions = $this->__get('required-actions');
            if (!empty($requiredActions)) {
                foreach ($requiredActions as $index => $action) {
                    $this->requiredActions[] = new ActionSpecification($action);
                }
            }
        }

        if ($this->has('optional-actions')) {
            $optionalActions = $this->__get('optional-actions');
            if (!empty($optionalActions)) {
                foreach ($optionalActions as $index => $action) {
                    $this->optionalActions[] = new ActionSpecification($action);
                }
            }
        }

        if ($this->has('required-events')) {
            $requiredEvents = $this->__get('required-events');
            if (!empty($requiredEvents)) {
                foreach ($requiredEvents as $index => $event) {
                    $this->requiredEvents[] = new EventSpecification($event);
                }
            }
        }

        if ($this->has('optional-events')) {
            $optionalEvents = $this->__get('optional-events');
            if (!empty($optionalEvents)) {
                foreach ($optionalEvents as $index => $event) {
                    $this->optionalEvents[] = new EventSpecification($event);
                }
            }
        }

        if ($this->has('required-properties')) {
            $requiredProperties = $this->__get('required-properties');
            if (!empty($requiredProperties)) {
                foreach ($requiredProperties as $index => $property) {
                    $this->requiredProperties[] = new PropertySpecification($property);
                }
            }
        }

        if ($this->has('optional-properties')) {
            $optionalProperties = $this->__get('optional-properties');
            if (!empty($optionalProperties)) {
                foreach ($optionalProperties as $index => $property) {
                    $this->optionalProperties[] = new PropertySpecification($property);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getRequiredActions()
    {
        return $this->requiredActions;
    }

    /**
     * @return mixed
     */
    public function getOptionalActions()
    {
        return $this->optionalActions;
    }

    /**
     * @return mixed
     */
    public function getRequiredEvents()
    {
        return $this->requiredEvents;
    }

    /**
     * @return mixed
     */
    public function getOptionalEvents()
    {
        return $this->optionalEvents;
    }

    /**
     * @return mixed
     */
    public function getRequiredProperties()
    {
        return $this->requiredProperties;
    }

    /**
     * @return mixed
     */
    public function getOptionalProperties()
    {
        return $this->optionalProperties;
    }
}
