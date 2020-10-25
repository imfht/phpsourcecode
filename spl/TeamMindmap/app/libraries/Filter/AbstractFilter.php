<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-11-15
 * Time: 下午9:22
 */

namespace Libraries\Filter;

use \Illuminate\Http\Request;
use \Illuminate\Routing\Route;
use \Response;

/**
 * 过滤类的抽象基类.
 *
 * Class AbstractFilter
 * @package Libraries\Filter
 */
abstract class AbstractFilter
{
    /**
     * 共外部调用的过滤方法，具体查看Laravel手册.
     *
     * @param Route $route
     * @param Request $request
     *
     */
    public function filter(Route $route, Request $request)
    {
        $this->initData($route, $request);

        $actionFilter = $this->getActionFilter();

        if( method_exists($this, $actionFilter) ){
            return $this->$actionFilter();
        } else {
            return $this->defaultFilter($route, $request);
        }
    }

    /**
     * 当找不具体的过滤处理函数时调用的方法，默认为不作任何处理.
     *
     * @param Route $route
     * @param Request $request
     */
    protected  function defaultFilter(Route $route, Request $request)
    {

    }

    /**
     * 返回具体处理过滤的方法的名称.
     *
     * @return string
     */
    protected  function getActionFilter()
    {
        return $this->currentAction . $this->filterPostfix;
    }

    /**
     * 设置具体处理过滤的方法名称
     *
     * @param $currentAction　　名称的前缀
     * @param null $postfix　　　名称的后缀，可不填，默认为'Filter'
     */
    protected function setCurrentAction($currentAction, $postfix = null)
    {
        $this->currentAction = $currentAction;

        if( ! is_null($postfix) ){
            $this->filterPostfix = $postfix;
        }
    }

    /**
     * 当过滤发现不合法访问时调用的以生成响应.
     *
     * @param string $errorMessage 错误提示信息，JSON对象的error属性的值.
     * @param int $code 返回的状态码，默认为200
     * @return \Illuminate\Http\JsonResponse
     */
    protected  function responseFailureInfo($errorMessage, $code = 200)
    {
        return Response::json([
            'error'=>$errorMessage
        ], $code);
    }


    /**
     * 在filter方法中被调用，用于进行初始化.
     *
     * @param Route $route
     * @param Request $request
     * @return mixed
     */
    abstract protected function initData(Route $route, Request $request);


    protected $filterPostfix = 'Filter';    //具体过滤处理方法的后缀

    protected $currentAction = null;    //具体处理过滤方法的前缀
}