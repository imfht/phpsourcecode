<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-11 03:27:21
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-13 01:10:19
 */


namespace common\services;

/**
 * 使用说明：
 * components 组件中添加如下配置
 *
 * 'service' => [
 *      'class' => 'common\services\BaseService'
 * ]
 *
 * 使用方式有二种:
 * 1）直接调取某个方法，其中的common为common全局，标识的为命名空间前缀。
 *    Yii::$app->service->commonClassName->method();
 *    实际调用的是  类 common\services\ClassName 方法为method;
 *
 *    也可以自定义一些，比如高级模板中的frontend,调用某个方法为:
 *    Yii::$app->service->frontendClassName->method();
 *    实际调用的是  类 frontend\services\ClassName 方法为method;
 *
 *
 * 2）使用属性namespace切换到某个命名空间，同一请求，后续的servce都是属于该命名空间。
 *    $service = Yii:$app->service;
 *    $service->namespace = 'frontend';
 *    $service->ClassName->method();
 *    实际调用的是  类 frontend\services\ClassName 方法为method;
 *
 *    通过$service调用的所有service均属于namespace赋值时前缀空间，即frontend。
 *    若切换到backend,直接再次赋值即可；
 *    $service->namespace = 'backend';
 *    $service->ClassName->method();
 *    实际调用的是  类 backend\services\ClassName 方法为method;
 *
 *
 * 注意： 该实现依从命名空间 common\services; services非必须，随初始命名空间，且全局影响，
 *      这也是该实现的不足，但已经够用；
 *      该值取决于当前文件BaseService的命名空间，进行命名空间转换时，只替换前缀，这里指common。
 */

class BaseService
{
    protected $namespace = __NAMESPACE__;

    protected $services;

    private $serviceName;

    private $serviceLocator;

    public function __construct()
    {
        $this->serviceLocator = new \yii\di\ServiceLocator();
        $this->services = [];
    }

    /**
     *
     * @param [type] $serviceName
     * @return void
     */
    public function __get($serviceName = null)
    {
        if ($serviceName ?? false) {
            return $this->get($serviceName);
        }
        throw new \Exception('Service ' . $serviceName . ' Not Exists.');
    }

    /**
     * 参数自定义创建，可切换命名空间
     *
     * @param [type] $serviceName
     * @param string $value
     */
    public function __set($serviceName, $value = '')
    {
        if ($serviceName == 'namespace') {
            $this->serviceName = $value;
            $this->changeNamespace();
            return null;
        }
    }

    /**
     * @param [type] $serviceName
     * @return void
     */
    private function get($serviceName)
    {
        $this->serviceName = $serviceName;
        $this->changeNamespace();
        $class = $this->combineClassName();
        $this->set($class);
        return $this->services[$class];
    }

    /**
     *
     * @param [type] $class 存储到服务的id名称
     * @return void
     */
    private function set($class)
    {
        if (!class_exists($class)) {
            throw new \Exception('Service ' . $class . ' Not Exists.');
        }

        if (!isset($this->services[$class])) {
            //这里对象的创建也可用 Yii::createObject();
            $this->serviceLocator->set($class, $class);
            $this->services[$class] = $this->serviceLocator->get($class);
        }
    }

    /**
     * 取得servcie前缀，以此判定命名空间
     *
     * @return void
     */
    private function interceptPrefix()
    {
        $arr = str_split($this->serviceName);
        $prefix = '';
        foreach ($arr as $letter) {
            $ord = ord($letter);
            if ($ord > 64 && $ord < 91) {
                break;
            }
            $prefix .= $letter;
        }
        return $prefix;
    }

    /**
     * 转换命名空间和servccie名称
     *
     * @return void
     */
    private function changeNamespace()
    {
        $prefix = $this->interceptPrefix();

        if (isset($prefix[0])) {
            $arr    = explode('\\', $this->namespace);

            $arr[2] = $prefix;
            $this->namespace   = implode('\\', $arr);
            //前缀转换后，重置serviceName
            $this->serviceName = substr($this->serviceName, strlen($prefix));
        }
    }

    /**
     * 取得有效的className
     *
     * @return void
     */
    private function combineClassName()
    {
        return $this->namespace . '\\' . $this->serviceName;
    }
}
