<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-24 17:37
 */
namespace Notadd\Mall\Controllers\Api\User;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\User\Vip\VipHandler;

/**
 * Class VipController.
 */
class VipController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\User\Vip\VipHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function vip(VipHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
