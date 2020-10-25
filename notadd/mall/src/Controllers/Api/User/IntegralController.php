<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-06-29 15:38
 */
namespace Notadd\Mall\Controllers\Api\User;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\User\Integral\IntegralHandler;
use Notadd\Mall\Handlers\User\Integral\ListHandler;

/**
 * Class IntegralController.
 */
class IntegralController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\User\Integral\IntegralHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function integral(IntegralHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\User\Integral\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
