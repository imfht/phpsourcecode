<?php declare(strict_types = 1);
namespace msqphp\test\base\arr;

// 继承测试类
class ArrTest extends \msqphp\test\Test
{
    public function testStart() : void
    {
        // 由于对应的测试类为Arr基类,全部为静态函数,所以在此处赋类值
        $this->class('\msqphp\base\arr\Arr');
        // 测试当前对象
        $this->testThis($this);
    }
    // 测试所有数组排序函数
    public function testSort() : void
    {
        // 清空数据,避免数据混乱造成错误
        $this->clear();
        // args,此处为待排序数组
        $args   = [1,2,465,47,136,49,136,49,13,496,31,74,64,46];
        // result,此处为结果,即排序后的数组
        $result = [1,2,13,31,46,47,49,49,64,74,136,136,465,496];
        // 因为参数与结果,所以统一赋值.
        $this->args($args)->result($result);
        // 分辨测试各个函数
        $this->method('bubbleSort')->test();
        $this->method('insertSort')->test();
        $this->method('quickSort')->test();
        $this->method('selectSort')->test();
    }
    public function testSetAndGet() : void
    {
        // 清空数据,避免数据混乱造成错误
        $this->clear();
        // args,此处为数据数组
        $args = [
            'liming'=>['name'=>'liming','age'=>12],
            'wangwu'=>['name'=>'wangwu','age'=>27],
            'zhanghai'=>['name'=>'zhanghai','age'=>50],
            'changni'=>['name'=>'changni','age'=>34],
        ];
        // 由于该函数为值引用传递,有些bug,所以人工测试
        \msqphp\base\arr\Arr::set($args, 'changni.age', 57);

        // 结果不为57,报错
        if ($args['changni']['age'] !== 57 || \msqphp\base\arr\Arr::get($args, 'changni.age') !== 57) {
            echo '该函数由于值为应用传递,所以采用非正常测试,此时测试失败';
            throw new \msqphp\test\TestException('测试失败');
        }
        echo '该函数由于值为应用传递,所以采用非正常测试,此时测试成功';
    }
    public function testRandom() : void
    {
        // 清空数据,避免数据混乱造成错误
        $this->clear();
        // 数组范围,1-19的单数
        $args = [1,3,5,7,9,11,13,15,17,19];
        // 方法,参数,结果以函数形式判断true通过,false则失败报错
        $this->method('random')->args($args)->result(function (int $value) use ($args) : bool {
            return in_array($value, $args);
        })->test();

        // 测试当数组中取多个值,
        $num = random_int(2, 7);

        $this->method('random')->args($args, $num)->result(function (array $value) use ($args, $num) : bool {
            $bool = true;
            $bool = count($value) === $num && $bool;
            foreach ($value as $number) {
                $bool = $bool && in_array($number, $args);
            }
            return $bool;
        })->test();
    }
}