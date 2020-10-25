<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:34
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Order\Rate\EditHandler;
use Notadd\Mall\Handlers\Administration\Order\Rate\ListHandler;
use Notadd\Mall\Handlers\Administration\Order\Rate\RateHandler;

/**
 * Class OrderRateController.
 */
class OrderRateController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::order-rate::edit' => 'edit',
        'global::mall-administration::order-rate::list' => 'list',
        'global::mall-administration::order-rate::rate' => 'rate',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Rate\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Rate\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Rate\RateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function rate(RateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
