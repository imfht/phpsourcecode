<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-23 13:50
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Order\OrderExpress\ConfigurationHandler;
use Notadd\Mall\Handlers\Seller\Order\OrderExpress\DeliveryHandler;
use Notadd\Mall\Handlers\Seller\Order\OrderExpress\OrderHandler;
use Notadd\Mall\Handlers\Seller\Order\OrderExpress\TemplateHandler;

/**
 * Class OrderExpressController.
 */
class OrderExpressController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\OrderExpress\ConfigurationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function configuration(ConfigurationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\OrderExpress\DeliveryHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function delivery(DeliveryHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\OrderExpress\OrderHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function order(OrderHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Order\OrderExpress\TemplateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function template(TemplateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
