<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 16:48
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Order\Express\ListHandler;
use Notadd\Mall\Handlers\Administration\Order\Express\TraceHandler;
use Notadd\Mall\Handlers\Administration\Order\Express\TypingHandler;

/**
 * Class OrderExpressController.
 */
class OrderExpressController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::order-express::list'   => 'list',
        'global::mall-administration::order-express::trace'  => 'trace',
        'global::mall-administration::order-express::typing' => 'typing',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Express\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Express\TraceHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function trace(TraceHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Express\TypingHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function typing(TypingHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
