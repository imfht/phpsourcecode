<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:33
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Product\Brand\ApplyHandler;
use Notadd\Mall\Handlers\Seller\Product\Brand\BrandHandler;
use Notadd\Mall\Handlers\Seller\Product\Brand\EditHandler;
use Notadd\Mall\Handlers\Seller\Product\Brand\ListHandler;
use Notadd\Mall\Handlers\Seller\Product\Brand\RevokeHandler;

/**
 * Class StoreBrandController.
 */
class StoreBrandController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-seller::store-brand::apply'  => 'apply',
        'global::mall-seller::store-brand::brand'  => 'brand',
        'global::mall-seller::store-brand::edit'   => 'edit',
        'global::mall-seller::store-brand::list'   => 'list',
        'global::mall-seller::store-brand::revoke' => 'revoke',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Brand\ApplyHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function apply(ApplyHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Brand\BrandHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function brand(BrandHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Brand\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Brand\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Brand\RevokeHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function revoke(RevokeHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
