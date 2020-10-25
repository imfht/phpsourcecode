<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-6
 * Time: 下午5:58.
 */

namespace MiotApi\Contract;

use MiotApi\Contract\Interfaces\Urn as UrnInterface;
use MiotApi\Exception\SpecificationErrorException;

class Urn implements UrnInterface
{
    /**
     * 符合 RFC 2141 的 URN正则规则.
     */
    const URN_REGEXP = '/^urn:[a-z0-9][a-z0-9-]{1,31}:([a-z0-9()+,-.:=@;$_!*\']|%(0[1-9a-f]|[1-9a-f][0-9a-f]))+$/i';

    /**
     * 分隔符.
     *
     * @var string
     */
    private $delimiter = ':';

    /**
     * 原始urn.
     *
     * @var
     */
    private $original;

    /**
     * 符合 RFC 2141 和 小米规范的 URN.
     *
     * @var
     */
    private $expression;

    /**
     * 小米 URN 规范所包含的字段
     * <URN> ::= "urn:"<namespace>":"<type>":"<name>":"<value>[":"<vendor-product>":"<version>][":"<template-uuid>].
     *
     * @var array
     */
    private $columns = [
        'urn',
        'namespace',
        'type',
        'name',
        'value',
        'vendor_product',
        'version',
        'template_uuid',
    ];

    /**
     * 各类型的预订义的基础urn.
     *
     * @var
     */
    private $baseUrn;

    /**
     * 预订义的小米 URN 规范所包含的字段
     * <URN> ::= "urn:"<namespace>":"<type>":"<name>":"<value>.
     *
     * @var array
     */
    private $baseColumns = [
        'urn',
        'namespace',
        'type',
        'name',
        'value',
    ];

    /**
     * 第一个字段必须为urn，否则视为非法urn.
     *
     * @var
     */
    private $urn = 'urn';

    /**
     * 如果是小米定义的规范为miot-spec
     * 蓝牙联盟定义的规范为bluetooth-spec.
     *
     * @var
     */
    private $namespace = 'miot-spec-v2';

    /**
     * 合法的namespace.
     *
     * @var array
     */
    private $validNamespaces = [
        'miot-spec',        // 小米定义的规范
        'miot-spec-v2',     // 小米定义的规范版本2
        'bluetooth-spec',   // 蓝牙联盟定义的规范
        'yeelink-spec',     // 为yeelink定义的规范
    ];

    /**
     * SpecificationType (类型，简写为: type)
     * 只能是如下几个:.
     *
     * property
     * action
     * event
     * service
     * device
     *
     * @var
     */
    private $type = 'property';

    /**
     * 合法的type.
     *
     * @var array
     */
    private $validTypes = [
        'property',     // 属性
        'action',       // 方法
        'event',        // 事件
        'service',      // 服务
        'device',       // 设备
    ];

    /**
     * 有意义的单词或单词组合(小写字母)
     * 多个单词用"-"间隔，比如：.
     *
     * temperature
     * current-temperature
     * device-name
     * battery-level
     *
     * @var
     */
    private $name;

    /**
     * name的正则
     * 单词或单词组合(小写字母)
     * 多个单词用"-"间隔.
     *
     * @var string
     */
    private $nameReg = '/^[a-z][a-z\-]*[a-z]$/';

    /**
     * 16进制字符串，使用UUID前8个字符，如：.
     *
     * 00002A06
     * 00002A00
     *
     * @var
     */
    private $value;

    /**
     * value正则
     * 16进制字符串，使用UUID前8个字符.
     *
     * @var string
     */
    private $valueReg = '/^([0-9A-Fa-f]{8})$/';

    /**
     * 厂家+产品代号 (这个字段只有在设备实例定义里出现)
     * 有意义的单词或单词组合(小写字母)，用"-"间隔，比如：.
     *
     * philips-moonlight
     * yeelink-c300
     * zhimi-vv
     * benz-c63
     *
     * @var string
     */
    private $vendorProduct;

    /**
     * 厂家+产品代号正则
     * 单词或单词组合(小写字母)
     * 多个单词用"-"间隔.
     *
     * @var string
     */
    private $vendorProductReg = '/^([a-z0-9\-]+)$/';

    /**
     * 版本号，只能是数字 (这个字段只有在设备实例定义里出现)
     * 如: 1, 2, 3.
     *
     * @var
     */
    private $version;

    /**
     * 版本号正则
     * 只能是数字.
     *
     * @var string
     */
    private $version_reg = '/^([0-9]+)$/';

    /**
     * 模板的uuid
     * 0000C801.
     *
     * @var
     */
    private $template_uuid;

    /**
     * 模板的uuid正则
     * 只能是数字.
     *
     * @var string
     */
    private $template_uuid_reg = '/^([0-9A-Za-z]+)$/';

    /**
     * Urn constructor.
     *
     * @param $urn
     *
     * @throws SpecificationErrorException
     */
    public function __construct($urn)
    {
        if (!$this->validate($urn)) {
            throw new SpecificationErrorException('Invalid URN!');
        }

        $this->original = $urn;

        // 执行解析
        $this->_parse();
    }

    /**
     * Validate a URN according to RFC 2141.
     *
     * @param $urn
     *
     * @return true when the URN is valid, FALSE when invalid
     *
     * @internal param the $urn URN to validate
     */
    private function validate($urn)
    {
        return (bool) preg_match(self::URN_REGEXP, $urn);
    }

    /**
     * @return mixed
     */
    public function getUrn()
    {
        return $this->urn;
    }

    /**
     * @param mixed $urn
     *
     * @throws SpecificationErrorException
     */
    public function setUrn($urn)
    {
        if ($urn != $this->urn) {
            throw new SpecificationErrorException('必须为urn，否则视为非法urn');
        }

        $this->urn = $urn;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     *
     * @throws SpecificationErrorException
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @throws SpecificationErrorException
     */
    public function setType($type)
    {
        if (!in_array($type, $this->validTypes)) {
            throw new SpecificationErrorException('非法 type');
        }

        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @throws SpecificationErrorException
     */
    public function setName($name)
    {
        if (!preg_match($this->nameReg, $name)) {
            throw new SpecificationErrorException('非法 name');
        }

        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @throws SpecificationErrorException
     */
    public function setValue($value)
    {
        if (!preg_match($this->valueReg, $value)) {
            throw new SpecificationErrorException('非法 value');
        }

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getVendorProduct()
    {
        return $this->vendorProduct;
    }

    /**
     * @param mixed $vendorProduct
     *
     * @throws SpecificationErrorException
     */
    public function setVendorProduct($vendorProduct)
    {
        if (!preg_match($this->vendorProductReg, $vendorProduct)) {
            throw new SpecificationErrorException('非法 厂家+产品代号');
        }

        $this->vendorProduct = $vendorProduct;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     *
     * @throws SpecificationErrorException
     */
    public function setVersion($version)
    {
        if (!preg_match($this->version_reg, $version)) {
            throw new SpecificationErrorException('非法 版本号');
        }

        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getTemplateUuid()
    {
        return $this->template_uuid;
    }

    /**
     * @param mixed $version
     *
     * @throws SpecificationErrorException
     */
    public function setTemplateUuid($template_uuid)
    {
        if (!preg_match($this->template_uuid_reg, $template_uuid)) {
            throw new SpecificationErrorException('非法 模板uuid');
        }

        $this->template_uuid = $template_uuid;
    }

    /**
     * @return mixed
     */
    public function getBaseUrn()
    {
        return $this->baseUrn;
    }

    /**
     * @return string
     */
    private function setBaseUrn()
    {
        $baseUrn = '';

        foreach ($this->baseColumns as $column) {
            $fncName = 'get'.ucfirst(
                    preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                        return strtoupper($match[1]);
                    }, $column)
                );

            if (method_exists($this, $fncName) && $this->{$fncName}()) {
                $baseUrn .= $this->delimiter.$this->{$fncName}();
            }
        }

        $this->baseUrn = trim($baseUrn, $this->delimiter);

        return $this->baseUrn;
    }

    /**
     * @return mixed
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * 根据各字段生成解析格式化后的urn.
     *
     * @return string
     */
    private function setExpression()
    {
        $expression = '';

        foreach ($this->columns as $column) {
            $fncName = 'get'.ucfirst(
                    preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                        return strtoupper($match[1]);
                    }, $column)
                );

            if (method_exists($this, $fncName) && $this->{$fncName}()) {
                $expression .= $this->delimiter.$this->{$fncName}();
            }
        }

        $this->expression = trim($expression, $this->delimiter);

        return $this->expression;
    }

    /**
     * urn 解析器.
     *
     * @return mixed
     */
    private function _parse()
    {
        $parses = explode($this->delimiter, $this->original);

        foreach ($this->columns as $index => $column) {
            $fncName = 'set'.ucfirst(
                    preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                        return strtoupper($match[1]);
                    }, $column)
                );
            if (method_exists($this, $fncName) && isset($parses[$index])) {
                $this->{$fncName}($parses[$index]);
            }
        }

        $this->setExpression();
        $this->setBaseUrn();

        return $this->expression;
    }
}
