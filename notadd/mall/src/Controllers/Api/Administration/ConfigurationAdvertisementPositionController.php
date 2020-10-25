<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 12:12
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\CreateHandler;
use Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\EditHandler;
use Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\ListHandler;
use Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\RemoveHandler;

/**
 * Class ConfigurationAdvertisementPositionController.
 */
class ConfigurationAdvertisementPositionController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Configuration\Advertisement\Position\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
