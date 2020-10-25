<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-09 11:57
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Store\Category\CategoryHandler;
use Notadd\Mall\Handlers\Admin\Store\Category\CreateHandler;
use Notadd\Mall\Handlers\Administration\Store\Category\EditHandler;
use Notadd\Mall\Handlers\Administration\Store\Category\ListHandler;
use Notadd\Mall\Handlers\Administration\Store\Category\RemoveHandler;
use Notadd\Mall\Handlers\Administration\Store\Category\RestoreHandler;

/**
 * Class ShopCategoryController.
 */
class StoreCategoryController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::store-category::category' => 'category',
        'global::mall-administration::store-category::edit'     => 'edit',
        'global::mall-administration::store-category::list'     => 'list',
        'global::mall-administration::store-category::remove'   => 'remove',
        'global::mall-administration::store-category::restore'  => 'restore',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Category\CategoryHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function category(CategoryHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Category\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Category\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Category\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Store\Category\RestoreHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function restore(RestoreHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
