<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-21 15:53
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Product\Brand\AccessHandler;
use Notadd\Mall\Handlers\Administration\Product\Brand\BrandHandler;
use Notadd\Mall\Handlers\Administration\Product\Brand\CreateHandler;
use Notadd\Mall\Handlers\Administration\Product\Brand\EditHandler;
use Notadd\Mall\Handlers\Administration\Product\Brand\ListHandler;
use Notadd\Mall\Handlers\Administration\Product\Brand\RemoveHandler;

/**
 * Class ProductBrandController.
 */
class ProductBrandController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::product-brand::access' => 'access',
        'global::mall-administration::product-brand::brand'  => 'brand',
        'global::mall-administration::product-brand::create' => 'create',
        'global::mall-administration::product-brand::edit'   => 'edit',
        'global::mall-administration::product-brand::list'   => 'list',
        'global::mall-administration::product-brand::remove' => 'remove',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Brand\AccessHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function access(AccessHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Brand\BrandHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function brand(BrandHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Brand\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Brand\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Brand\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Brand\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
