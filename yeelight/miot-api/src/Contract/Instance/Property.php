<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-6
 * Time: 下午6:25.
 */

namespace MiotApi\Contract\Instance;

use MiotApi\Contract\Specification\PropertySpecification;
use MiotApi\Contract\Urn;
use MiotApi\Exception\SpecificationErrorException;
use MiotApi\Util\Collection\Arr;
use MiotApi\Util\Collection\Collection;

class Property extends PropertySpecification
{
    protected $data;

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
        $this->collection = new Collection($this->data);

        $this->init();
    }

    public function init()
    {
        $this->iid = $this->collection->get('iid');
        $this->urn = new Urn($this->collection->get('type'));

        if ($this->has('format')) {
            $format = $this->__get('format');
            if (!in_array($format, $this->formatMap)) {
                throw new SpecificationErrorException('属性-》数据格式(format)的取值不在合法范围内');
            }
            $this->format = $format;
        }

        if ($this->has('access')) {
            $access = $this->__get('access');
            if (!empty($access)) {
                foreach ($access as $item) {
                    if (!in_array($item, $this->accessMap)) {
                        throw new SpecificationErrorException('属性-》访问方式(access)的取值不在合法范围内');
                    }
                }
                $this->access = $access;
            } else {
                $this->access = [];
            }
        }

        if ($this->has('value-range')) {
            $valueRange = $this->__get('value-range');
            $this->valueRange = $valueRange;
        }

        if ($this->has('value-list')) {
            $valueList = $this->__get('value-list');

            // TODO 当format为整型，可定义"value-list"的验证
            $this->valueList = $valueList;
        }

        if ($this->has('unit')) {
            $unit = $this->__get('unit');
            if (!in_array($unit, $this->unitMap)) {
                throw new SpecificationErrorException('属性-》单位(unit)的取值不在合法范围内');
            }

            // TODO 当format为整型或浮点型，可定义unit值 的验证
            $this->unit = $unit;
        }

        $this->specification = new PropertySpecification($this->urn->getBaseUrn());
    }

    public function getIid()
    {
        return $this->iid;
    }

    /**
     * 验证给定的值是否 符合 format.
     *
     * @param $value
     *
     * @return bool
     */
    public function verify($value)
    {
        if ($this->has('value-range')) {
            $valueRange = $this->get('value-range');
            if ($value > $valueRange[1] || $value < $valueRange[0]) {
                return false;
            }
        }

        if ($this->has('value-list')) {
            $valueList = $this->get('value-list');
            $valueList = Arr::pluck($valueList, 'value');

            if (!in_array($value, $valueList)) {
                return false;
            }
        }

        switch ($this->format) {
            case 'bool':
                return in_array($value, [
                    true,
                    false,
                    1,
                    0,
                ]);
                break;
            case 'uint8':
            case 'uint16':
            case 'uint32':
            case 'int8':
            case 'int16':
            case 'int32':
            case 'int64':
                return is_int($value);
                break;
            case 'float':
                return is_float($value);
                break;
            case 'string':
                return is_string($value);
                break;
        }
    }

    public function canRead()
    {
        return in_array('read', $this->access);
    }

    public function canWrite()
    {
        return in_array('write', $this->access);
    }

    public function canNotify()
    {
        return in_array('notify', $this->access);
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
