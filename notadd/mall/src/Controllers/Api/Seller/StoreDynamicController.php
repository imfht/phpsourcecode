<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 17:13
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\ConfigurationHandler;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\CreateHandler;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\DynamicHandler;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\EditHandler;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\ListHandler;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\RemoveHandler;
use Notadd\Mall\Handlers\Seller\Store\Dynamic\RestoreHandler;

/**
 * Class StoreDynamicController.
 */
class StoreDynamicController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-seller::store-dynamic::configuration' => 'configuration',
        'global::mall-seller::store-dynamic::create'        => 'create',
        'global::mall-seller::store-dynamic::dynamic'       => 'dynamic',
        'global::mall-seller::store-dynamic::edit'          => 'edit',
        'global::mall-seller::store-dynamic::list'          => 'list',
        'global::mall-seller::store-dynamic::remove'        => 'remove',
        'global::mall-seller::store-dynamic::restore'       => 'restore',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\ConfigurationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function configuration(ConfigurationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\DynamicHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function dynamic(DynamicHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Dynamic\RestoreHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function restore(RestoreHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
