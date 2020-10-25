<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{

    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__ . '/../../bootstrap/start.php';
    }

    /**
     * 实例化指定类的mock对象.
     *
     * @param $className 待实例化的类
     * @return \Mockery\MockInterface 返回得到的mock对象实例
     */
    public function mock($className)
    {
        $mock = Mockery::mock($className);
        $this->app->instance($className, $mock);

        return $mock;
    }

    /**
     * 对框架内的assertArrayHasKey进行扩展，用于测试数组是否具备多个键值.
     *
     * @param mixed $dataArray  待测试的数组
     * @param array $keys 待测试的键值
     * @param bool $defined 是否测试该键值不为null，默认为不测试
     */
    protected function arrayMustHasKeys($dataArray, array $keys, $defined = false)
    {
        foreach($keys as $currentKey){
            $this->assertArrayHasKey($currentKey, $dataArray);
            if( $defined ){
                $this->assertNotNull( $dataArray[ $currentKey ] );
            }
        }
    }

    /**
     * 对内置assertEquals的封装，测试两个数组含有值相同的键值对.
     *
     * @param array $arrayL
     * @param array $arrayR
     * @param array $keys
     */
    protected function arrayMustHasEqualKeyValues(array $arrayL, array $arrayR, array $keys)
    {
        foreach ($keys as $currentKey) {
            $this->assertEquals($arrayL[$currentKey], $arrayR[$currentKey]);
        }
    }

    /**
     * 对Facade Input的get方法进行模拟.
     *
     * @param array $values 待模拟的键值对
     * @param int $times 调用的次数，默认为一次
     *
     * 示例：
     *  假如控制器中会通过Input::get方法获得数据：
     *  $name = Input::get('name');
     *
     *  期待$name的值是: admin
     *  则使用如下的方法：
     *  $this->mockInputFacadeGet(['name'=>'admin');
     */
    protected function mockInputFacadeGet(array $values, $times = 1)
    {
        foreach($values as $key=>$val){
            Input::shouldReceive('get')->times($times)->with($key)->andReturn($val);
        }
    }

    /**
     * 对Facade Input的all方法进行模拟.
     *
     * @param array $values 待模拟的键值对
     * @param int $times 调用的次数，默认为一次
     * 示例：
     *  假如控制器中会通过Input::get方法获得数据：
     *  $postData = Input::all();
     *
     *  期待$postData的值是: ['name'=>'admin']
     *  则使用如下的方法：
     *  $this->mockInputFacadeAll(['name'=>'admin');
     */
    protected function mockInputFacadeAll(array $values, $times = 1)
    {
        Input::shouldReceive('all')->times($times)->andReturn($values);
    }

    /**
     * 设置模型查找失败的异常
     */
    protected function setModelNotFoundException()
    {
        $this->setExpectedException('\Illuminate\Database\Eloquent\ModelNotFoundException');
    }

    /**
     * 随机登陆一个用户，并返回该用户所对应的User模型实例.
     *
     * @param bool $seedDB 是否进行数据库填从，默认为否
     * @param bool $login 是否进行用户登陆，默认为登陆
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    protected function getTestUser($seedDB = false, $login = true)
    {
        if( $seedDB ){
            $this->seed('UserTableTestSeeder');
        }

        $testUser = User::firstOrFail();
        if( $login ){
            $this->be($testUser);
        }

        return $testUser;
    }
}
