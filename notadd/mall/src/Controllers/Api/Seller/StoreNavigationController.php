<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 17:33
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Store\Navigation\CreateHandler;
use Notadd\Mall\Handlers\Seller\Store\Navigation\EditHandler;
use Notadd\Mall\Handlers\Seller\Store\Navigation\ListHandler;
use Notadd\Mall\Handlers\Seller\Store\Navigation\NavigationHandler;
use Notadd\Mall\Handlers\Seller\Store\Navigation\RemoveHandler;

/**
 * Class StoreNavigationController.
 */
class StoreNavigationController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-seller::store-navigation::create'     => 'create',
        'global::mall-seller::store-navigation::edit'       => 'edit',
        'global::mall-seller::store-navigation::list'       => 'list',
        'global::mall-seller::store-navigation::navigation' => 'navigation',
        'global::mall-seller::store-navigation::remove'     => 'remove',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Navigation\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Navigation\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Navigation\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Navigation\NavigationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function navigation(NavigationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Navigation\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
