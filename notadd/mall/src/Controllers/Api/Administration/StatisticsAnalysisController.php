<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-22 16:42
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Statistics\Analysis\DashBoardHandler;
use Notadd\Mall\Handlers\Administration\Statistics\Analysis\IndustryHandler;
use Notadd\Mall\Handlers\Administration\Statistics\Analysis\PriceHandler;

/**
 * Class StatisticsAnalysisController.
 */
class StatisticsAnalysisController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Analysis\DashBoardHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function dashboard(DashBoardHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Analysis\IndustryHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function industry(IndustryHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Analysis\PriceHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function price(PriceHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
