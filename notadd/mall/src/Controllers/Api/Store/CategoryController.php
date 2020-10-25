<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 10:36
 */
namespace Notadd\Mall\Controllers\Api\Store;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Store\Category\CategoryHandler;
use Notadd\Mall\Handlers\Store\Category\ListHandler;

/**
 * Class CategoryController.
 */
class CategoryController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Store\Category\CategoryHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function category(CategoryHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Store\Category\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
