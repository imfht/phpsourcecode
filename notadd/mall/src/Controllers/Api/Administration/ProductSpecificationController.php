<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-05 20:39
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Product\Specification\CreateHandler;
use Notadd\Mall\Handlers\Administration\Product\Specification\EditHandler;
use Notadd\Mall\Handlers\Administration\Product\Specification\ListHandler;
use Notadd\Mall\Handlers\Administration\Product\Specification\RemoveHandler;
use Notadd\Mall\Handlers\Administration\Product\Specification\SpecificationHandler;

/**
 * Class SpecificationController.
 */
class ProductSpecificationController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::product-specification::create'        => 'create',
        'global::mall-administration::product-specification::edit'          => 'edit',
        'global::mall-administration::product-specification::list'          => 'list',
        'global::mall-administration::product-specification::remove'        => 'remove',
        'global::mall-administration::product-specification::specification' => 'specification',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Specification\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Specification\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Specification\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Specification\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Product\Specification\SpecificationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function specification(SpecificationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
