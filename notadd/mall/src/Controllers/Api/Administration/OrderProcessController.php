<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 15:07
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Order\Process\ConfirmHandler;
use Notadd\Mall\Handlers\Administration\Order\Process\CreateHandler;
use Notadd\Mall\Handlers\Administration\Order\Process\FinishHandler;
use Notadd\Mall\Handlers\Administration\Order\Process\PayHandler;
use Notadd\Mall\Handlers\Administration\Order\Process\SendHandler;

/**
 * Class OrderProcessController.
 */
class OrderProcessController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Process\ConfirmHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function confirm(ConfirmHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
    
    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Process\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Process\FinishHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function finish(FinishHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Process\PayHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function pay(PayHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Process\SendHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function send(SendHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
