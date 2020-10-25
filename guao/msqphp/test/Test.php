<?php declare(strict_types = 1);
namespace msqphp\test;

use msqphp\base;

abstract class Test
{
    use TestPointerTrait;
    use TestStaticTrait;

    /**
     * 不进行测试的函数名称
     * @var  array
     */
    protected $not_test_functions = [
        'test',
        'testStart',
        'testThis',
        'testAllTestClassesByDir',
        'testFunction',
        'testObjectProperty',
        'testObjectMethod',
        'testClassStaticMethod',
        'testClassStaticProperty'
    ];

    // 每个测试类开始的函数
    abstract public function testStart() : void;

    // 测试当前类
    final protected function testThis() : void
    {
        foreach($this->getAllClassMethods($this) as $method) {
            static::testObjectMethod($this, $method);
        }
    }

    /**
     * 测试目标目录所有测试类文件的所有测试函数
     * @param   string  $dir_path  目标目录
     *
     * @return  void
     */
    final protected function testAllTestClassesByDir(string $dir_path) : void
    {
        if (!is_dir($dir_path)) {
            throw new TestException('指定测试目录不存在,无法进一步测试.');
        }

        $declared_classes = get_declared_classes();

        foreach (base\dir\Dir::getAllFileByType($dir_path, 'Test.php') as $file) {
            require_once $file;
        }

        $all_declared_classes = get_declared_classes();

        $new = array_filter($all_declared_classes, function ($class) use ($declared_classes) {
            return $class !== 'stdClass' && !array_search($class, $declared_classes);
        });

        array_walk($new, function(string $class) : void {
            $obj = new $class;
            if ($obj instanceof \msqphp\test\Test) {
                $obj->testStart();
            }
            unset($obj);
        });
    }

    /**
     * 得到目标对象所有方法
     * @param   obj   $obj  目标对象
     *
     * @return  array
     */
    final protected function getAllClassMethods($obj) : array
    {
        return array_filter(get_class_methods($obj), function(string $method) : bool {
            return substr($method, 0, 4) === 'test' && !in_array($method, $this->not_test_functions);
        });
    }
}