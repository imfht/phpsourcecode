<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-23 11:53
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Service\Refund\ListHandler;
use Notadd\Mall\Handlers\Seller\Service\Refund\ProcessHandler;
use Notadd\Mall\Handlers\Seller\Service\Refund\RefundHandler;

/**
 * Class ServiceRefundController.
 */
class ServiceRefundController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Seller\Service\Refund\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Service\Refund\ProcessHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function process(ProcessHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Service\Refund\RefundHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function refund(RefundHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
