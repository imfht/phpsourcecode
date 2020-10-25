<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-23 12:23
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Order\ListHandler;
use Notadd\Mall\Handlers\Seller\Order\OrderHandler;
use Notadd\Mall\Handlers\Seller\Order\ProcessHandler;

/**
 * Class OrderController.
 */
class OrderController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-seller::order::list'    => 'list',
        'global::mall-seller::order::order'   => 'order',
        'global::mall-seller::order::process' => 'process',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\OrderHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function order(OrderHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\ProcessHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function process(ProcessHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
