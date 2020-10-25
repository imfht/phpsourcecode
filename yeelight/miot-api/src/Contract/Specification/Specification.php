<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-7
 * Time: 下午5:49.
 */

namespace MiotApi\Contract\Specification;

use MiotApi\Contract\Interfaces\Specification as SpecificationInterface;
use MiotApi\Contract\RemoteSpec;
use MiotApi\Contract\Urn;
use MiotApi\Util\Collection\Collection;

abstract class Specification implements SpecificationInterface
{
    protected $collection;

    protected $urn;

    /**
     * 描述: 纯文本字段.
     *
     * @var
     */
    protected $description;

    /**
     * Specification constructor.
     *
     * @param $urn
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     */
    public function __construct($urn)
    {
        $this->urn = new Urn($urn);
        $this->init();
    }

    public function init()
    {
        $instanceType = $this->urn->getType();
        $items = RemoteSpec::{$instanceType}($this->urn->getBaseUrn());
        $this->collection = new Collection($items);
    }

    public function getUrn()
    {
        return $this->urn;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function toContext()
    {
        return $this->toJson();
    }

    public function toCollection()
    {
        return $this->collection;
    }

    public function toJson()
    {
        return $this->collection->toJson();
    }

    public function toArray()
    {
        return $this->collection->toArray();
    }

    public function __get($key)
    {
        return $this->collection->offsetGet($key);
    }

    /**
     * Proxy a method call onto the collection items.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->collection->{$method}(...$parameters);
    }
}
