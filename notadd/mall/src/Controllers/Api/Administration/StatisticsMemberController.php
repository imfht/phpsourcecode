<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-22 17:10
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Statistics\Member\AreaHandler;
use Notadd\Mall\Handlers\Administration\Statistics\Member\MemberHandler;
use Notadd\Mall\Handlers\Administration\Statistics\Member\NewlyHandler;

/**
 * Class StatisticsMemberController.
 */
class StatisticsMemberController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Member\AreaHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function area(AreaHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Member\MemberHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function member(MemberHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Member\NewlyHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function newly(NewlyHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
