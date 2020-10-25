<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 11:23
 */
namespace Notadd\Mall\Controllers\Api\Store;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Store\ApplyHandler;
use Notadd\Mall\Handlers\Store\ListHandler;
use Notadd\Mall\Handlers\Store\StoreHandler;
use Notadd\Mall\Handlers\Store\TypeHandler;

/**
 * Class StoreController.
 */
class StoreController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Store\ApplyHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function apply(ApplyHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Store\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Store\StoreHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function store(StoreHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Store\TypeHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function type(TypeHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
