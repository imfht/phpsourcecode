<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 12:16
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Store\Dynamic\DynamicHandler;
use Notadd\Mall\Handlers\Administration\Store\Dynamic\EditHandler;
use Notadd\Mall\Handlers\Administration\Store\Dynamic\ListHandler;
use Notadd\Mall\Handlers\Administration\Store\Dynamic\RemoveHandler;

/**
 * Class ShopDynamicController.
 */
class StoreDynamicController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::store-dynamic::dynamic' => 'dynamic',
        'global::mall-administration::store-dynamic::edit'    => 'edit',
        'global::mall-administration::store-dynamic::list'    => 'list',
        'global::mall-administration::store-dynamic::remove'  => 'remove',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Dynamic\DynamicHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function dynamic(DynamicHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Dynamic\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Dynamic\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Dynamic\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
