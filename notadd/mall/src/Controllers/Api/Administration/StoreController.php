<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 16:13
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Store\CreateHandler;
use Notadd\Mall\Handlers\Administration\Store\EditHandler;
use Notadd\Mall\Handlers\Administration\Store\ListHandler;
use Notadd\Mall\Handlers\Administration\Store\RemoveHandler;
use Notadd\Mall\Handlers\Administration\Store\RestoreHandler;

/**
 * Class StoreController.
 */
class StoreController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::store::create'  => 'create',
        'global::mall-administration::store::edit'    => 'edit',
        'global::mall-administration::store::list'    => 'list',
        'global::mall-administration::store::remove'  => 'remove',
        'global::mall-administration::store::restore' => 'restore',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\CreateHandler $handler
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\RestoreHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function restore(RestoreHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
