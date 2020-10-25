<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:23
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Order\Refund\ConfirmHandler;
use Notadd\Mall\Handlers\Administration\Order\Refund\FinishHandler;
use Notadd\Mall\Handlers\Administration\Order\Refund\ListHandler;

/**
 * Class OrderRefundController.
 */
class OrderRefundController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::order-refund::confirm' => 'confirm',
        'global::mall-administration::order-refund::finish'  => 'finish',
        'global::mall-administration::order-refund::list'    => 'list',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Refund\ConfirmHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function confirm(ConfirmHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Refund\FinishHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function finish(FinishHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Refund\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
