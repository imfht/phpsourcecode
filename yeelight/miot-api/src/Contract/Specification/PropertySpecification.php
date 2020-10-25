<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-8
 * Time: 下午3:43.
 */

namespace MiotApi\Contract\Specification;

use MiotApi\Exception\SpecificationErrorException;

/**
 * 属性描述需要表达这几个意思:
 * 语义是什么？
 * 数据格式是什么？
 * 是否可读？是否可写？数据变化了是否有通知？
 * 值是否有约束？如果有，取值范围是离散值还是连续值？
 * 单位是否定义？如果有定义，单位是什么？
 *
 * Class PropertySpecification
 */
class PropertySpecification extends Specification
{
    /**
     * 数据格式.
     *
     * 数据格式    描述
     * bool        布尔值: true/false 或 1/0
     * uint8    无符号8位整型
     * uint16    无符号16位整型
     * uint32    无符号32位整型
     * int8        有符号8位整型
     * int16    有符号16位整型
     * int32    有符号32位整型
     * int64    有符号64位整型
     * float    浮点数
     * string    字符串
     *
     * @var
     */
    protected $format;

    protected $formatMap = [
        'bool',
        'uint8',
        'uint16',
        'uint32',
        'int8',
        'int16',
        'int32',
        'int64',
        'float',
        'string',
    ];

    /**
     * 访问方式
     * 值        描述
     * read        读
     * write    写
     * notify    通知.
     *
     * @var
     */
    protected $access;

    protected $accessMap = [
        'read',
        'write',
        'notify',
    ];

    /**
     * 对取值范围进行约束，可选字段
     * 当format为整型或浮点数，可定义value-range，比如：
     * 最小值        最大值    步长
     * 16        32        0.5.
     *
     * @var
     */
    protected $valueRange;

    /**
     * 对取值范围进行约束，可选字段
     * 当format为整型，可定义"value-list"，每个元素都包含：value description.
     *
     * @var
     */
    protected $valueList;

    /**
     * 单位，可选字段
     * 当format为整型或浮点型，可定义unit值
     *
     * 值            描述
     * percentage    百分比
     * celsius        摄氏度
     * senconds        秒
     * minutes        分
     * hours        小时
     * days            天
     * kelvin        开氏温标
     * pascal        帕斯卡(大气压强单位)
     * arcdegrees    弧度(角度单位)
     *
     * @var
     */
    protected $unit;

    protected $unitMap = [
        'percentage',
        'celsius',
        'senconds',
        'minutes',
        'hours',
        'days',
        'kelvin',
        'pascal',
        'arcdegrees',
        'rgb',
    ];

    /**
     * @throws SpecificationErrorException
     */
    public function init()
    {
        parent::init();

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
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @return mixed
     */
    public function getValueRange()
    {
        return $this->valueRange;
    }

    /**
     * @return mixed
     */
    public function getValueList()
    {
        return $this->valueList;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
