<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 20:03
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Product\Specification\CreateHandler;
use Notadd\Mall\Handlers\Seller\Product\Specification\EditHandler;
use Notadd\Mall\Handlers\Seller\Product\Specification\ListHandler;
use Notadd\Mall\Handlers\Seller\Product\Specification\RemoveHandler;
use Notadd\Mall\Handlers\Seller\Product\Specification\SpecificationHandler;

/**
 * Class ProductSpecificationsController.
 */
class ProductSpecificationController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-seller::product-specification::create'         => 'create',
        'global::mall-seller::product-specification::edit'           => 'edit',
        'global::mall-seller::product-specification::list'           => 'list',
        'global::mall-seller::product-specification::remove'         => 'remove',
        'global::mall-seller::product-specification::specifications' => 'specifications',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Specification\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Specification\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Specification\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Specification\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Product\Specification\SpecificationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function specifications(SpecificationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
