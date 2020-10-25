<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-01 00:21
 */

namespace Notadd\Mall\Controllers\Api\Store;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Store\Product\Category\ListHandler;

/**
 * Class ProductCategoryController.
 */
class ProductCategoryController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Store\Product\Category\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
