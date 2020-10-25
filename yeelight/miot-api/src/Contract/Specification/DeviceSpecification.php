<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午3:43.
 */

namespace MiotApi\Contract\Specification;

/**
 * 设备是一个独立的有意义的设备，比如：灯泡、插座、风扇。
 * 描述一个设备，需要说清楚：是什么设备？有哪些服务可用？
 *
 * Class DeviceSpecification
 */
class DeviceSpecification extends Specification
{
    /**
     * 必选服务
     *
     * @var
     */
    protected $requiredServices;

    /**
     * 可选服务
     *
     * @var
     */
    protected $optionalServices;

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function init()
    {
        parent::init();

        if ($this->has('required-services')) {
            $requiredServices = $this->__get('required-services');
            if (!empty($requiredServices)) {
                foreach ($requiredServices as $index => $service) {
                    $this->requiredServices[] = new ServiceSpecification($service);
                }
            }
        }

        if ($this->has('required-services')) {
            $optionalServices = $this->__get('optional-services');
            if (!empty($optionalServices)) {
                foreach ($optionalServices as $index => $service) {
                    $this->optionalServices[] = new ServiceSpecification($service);
                }
            }
        }
    }

    /**
     * 获取 必选服务
     *
     * @return mixed
     */
    public function getRequiredServices()
    {
        return $this->requiredServices;
    }

    /**
     * 获取 可选服务
     *
     * @return mixed
     */
    public function getOptionalServices()
    {
        return $this->optionalServices;
    }
}
