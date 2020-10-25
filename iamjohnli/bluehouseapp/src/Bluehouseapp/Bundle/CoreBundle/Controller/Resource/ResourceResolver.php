<?php


namespace Bluehouseapp\Bundle\CoreBundle\Controller\Resource;

use Bluehouseapp\Bundle\CoreBundle\Doctrine\ORM\RepositoryInterface;

/**
 * Resource resolver.
 *
 */
class ResourceResolver
{
    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Get resources via repository based on the configuration.
     *
     * @param RepositoryInterface $repository
     * @param string              $defaultMethod
     * @param array               $defaultArguments
     *
     * @return mixed
     */
    public function getResource(RepositoryInterface $repository, $defaultMethod, array $defaultArguments = array())
    {
        $callable = array($repository, $this->config->getMethod($defaultMethod));
        $arguments = $this->config->getArguments($defaultArguments);

        return call_user_func_array($callable, $arguments);
    }

    /**
     * Create resource.
     *
     * @param RepositoryInterface $repository
     * @param string              $defaultMethod
     * @param array               $defaultArguments
     *
     * @return mixed
     */
    public function createResource(RepositoryInterface $repository, $defaultMethod, array $defaultArguments = array())
    {
        $callable = array($repository, $this->config->getFactoryMethod($defaultMethod));
        $arguments = $this->config->getFactoryArguments($defaultArguments);

        return call_user_func_array($callable, $arguments);
    }
}
/**
call_user_func_array — 调用回调函数，并把一个数组参数作为回调函数的参数

说明 ¶

mixed call_user_func_array ( callable $callback , array $param_arr )
把第一个参数作为回调函数（callback）调用，把参数数组作（param_arr）为回调函数的的参数传入。

参数 ¶

callback
被调用的回调函数。

param_arr
要被传入回调函数的数组，这个数组得是索引数组。

返回值 ¶

返回回调函数的结果。如果出错的话就返回FALSE

更新日志 ¶
 */