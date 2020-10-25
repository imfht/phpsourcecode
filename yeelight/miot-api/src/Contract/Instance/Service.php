<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-6
 * Time: 下午6:25.
 */

namespace MiotApi\Contract\Instance;

use MiotApi\Contract\Specification\ServiceSpecification;
use MiotApi\Contract\Specification\Specification;
use MiotApi\Contract\Urn;
use MiotApi\Util\Collection\Collection;

class Service extends Specification
{
    protected $data;

    protected $propertiesNode;

    /**
     * 实例ID(Instance ID，简称iid).
     *
     * @var
     */
    protected $iid;

    /**
     * type对象
     *
     * @var
     */
    protected $specification;

    public function __construct($data = [])
    {
        $this->data = $data;
        $this->init();
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function init()
    {
        $this->collection = new Collection($this->data);
        $this->iid = $this->collection->get('iid');
        $this->urn = new Urn($this->collection->get('type'));

        $this->specification = new ServiceSpecification($this->urn->getBaseUrn());
        $this->initProperties();
    }

    /**
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    protected function initProperties()
    {
        if ($this->has('properties')) {
            $properties = $this->get('properties');
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    $this->propertiesNode[$property['iid']] = new Property($property);
                }
            }
        }
    }

    public function getIid()
    {
        return $this->iid;
    }

    public function property($piid)
    {
        return $this->propertiesNode[$piid];
    }

    public function getPropertiesNode()
    {
        return $this->propertiesNode;
    }

    public function getSpecification()
    {
        return $this->specification;
    }

    public function getSpecificationContext()
    {
        return $this->specification->toContext();
    }
}
