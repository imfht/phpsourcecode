<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-27 17:05
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Store\Information\InformationHandler;
use Notadd\Mall\Handlers\Seller\Store\Information\RenewHandler;

/**
 * Class StoreInformationController.
 */
class StoreInformationController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Information\InformationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function information(InformationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Information\RenewHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function renew(RenewHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
