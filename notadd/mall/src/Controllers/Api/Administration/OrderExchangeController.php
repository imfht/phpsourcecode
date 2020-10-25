<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:03
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Order\Exchange\ConfirmHandler;
use Notadd\Mall\Handlers\Administration\Order\Exchange\FinishHandler;
use Notadd\Mall\Handlers\Administration\Order\Exchange\ListHandler;
use Notadd\Mall\Handlers\Administration\Order\Exchange\SendHandler;

/**
 * Class OrderExchangeController.
 */
class OrderExchangeController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::order-exchange::confirm' => 'confirm',
        'global::mall-administration::order-exchange::finish'  => 'finish',
        'global::mall-administration::order-exchange::list'    => 'list',
        'global::mall-administration::order-exchange::send'    => 'send',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Exchange\ConfirmHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function confirm(ConfirmHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Exchange\FinishHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function finish(FinishHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Exchange\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Exchange\SendHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function send(SendHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
