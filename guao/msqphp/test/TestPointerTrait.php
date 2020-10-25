<?php declare(strict_types = 1);
namespace msqphp\test;

trait TestPointerTrait
{
    public $pointer = [];

    // 初始化当前对象所有数据
    final public function init() : self
    {
        $this->pointer = [];

        return $this;
    }

    // 清楚当前对象测试数据(即清楚除类和对象以外的所有数据)
    final public function clear() : self
    {
        if (isset($this->pointer['class'])) {
            $this->pointer= ['class'=>$this->pointer['class']];
        } elseif (isset($this->pointer['object'])) {
            $this->pointer = ['object'=>$this->pointer['object']];
        } else {
            $this->pointer = [];
        }
        return $this;
    }

    // 指定测试类
    final public function class(string $class) : self
    {
        $this->pointer['class'] = $class;
        return $this;
    }

    // 指定测试对象,需要赋值一个对象
    final public function object($object) : self
    {
        $this->pointer['object'] = $object;
        return $this;
    }

    // 方法名称
    final public function method(string $method) : self
    {
        $this->pointer['method'] = $method;
        return $this;
    }

    // 函数名称或者一个函数(不推荐直接放置函数,一般用不到)
    final public function func($func) : self
    {
        $this->pointer['func'] = $func;
        return $this;
    }

    // 待测试函数所需参数
    final public function args() : self
    {
        $this->pointer['args'] = func_get_args();
        return $this;
    }

    // 函数调用结果
    final public function result($result) : self
    {
        $this->pointer['result'] = $result;
        return $this;
    }

    // 属性
    final public function property(string $property) : self
    {
        $this->pointer['property'] = $property;
        return $this;
    }
    // 值,配合property,进行属性测试时使用
    final public function value($value) : self
    {
        $this->pointer['value'] = $value;
        return $this;
    }

    // 链式操作 [[string $method, array $args], [string $method, array $args] ....]
    final public function chain($chain) : self
    {
        $this->pointer['chain'] = $chain;
        return $this;
    }

    // 进行一次测试
    final public function test() : void
    {
        $pointer = $this->pointer;
        if (isset($pointer['method'])) {
            $pointer['result'] = $pointer['result'] ?? null;
            if (isset($pointer['class'])) {
                static::testClassStaticMethod($pointer['class'], $pointer['method'], $pointer['args'], $pointer['result']);
            } elseif (isset($pointer['object'])) {
                $pointer['result'] === $this && $pointer['result'] = $pointer['object'];
                static::testObjectMethod($pointer['object'], $pointer['method'], $pointer['args'], $pointer['result']);
            }
        } elseif (isset($pointer['chain'])) {
            for ($i = 0, $l = count($pointer['chain']) - 1; $i < $l; ++$i) {
                static::testObjectMethod($pointer['object'], array_shift($pointer['chain'][$i]), $pointer['chain'][$i], $pointer['object']);
            }
            static::testObjectMethod($pointer['object'], array_shift($pointer['chain'][$i]), $pointer['chain'][$i], $pointer['result'] ?? null);
        } elseif (isset($pointer['func'])) {
            static::testFunc($pointer['func'], $pointer['args'], $pointer['result'] ?? null);
        } elseif (isset($pointer['property'])) {
            if (isset($pointer['class'])) {
                static::testClassStaticProperty($pointer['class'], $pointer['property'], $pointer['value']);
            } elseif (isset($pointer['object'])) {
                static::testObjectProperty($pointer['object'], $pointer['property'], $pointer['value']);
            }
        } else {
            throw new TestException('不正确的测试方式');
        }
    }
}
