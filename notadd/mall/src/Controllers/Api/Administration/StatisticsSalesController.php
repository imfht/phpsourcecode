<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-22 18:15
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Statistics\Sales\IncomeHandler;
use Notadd\Mall\Handlers\Administration\Statistics\Sales\OrderHandler;

/**
 * Class StatisticsSalesController.
 */
class StatisticsSalesController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Sales\IncomeHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function income(IncomeHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Statistics\Sales\OrderHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function order(OrderHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
