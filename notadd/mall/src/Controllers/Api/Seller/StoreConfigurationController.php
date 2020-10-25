<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 18:09
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Store\Configuration\CarouselHandler;
use Notadd\Mall\Handlers\Seller\Store\Configuration\ConfigurationHandler;
use Notadd\Mall\Handlers\Seller\Store\Configuration\SettingsHandler;

/**
 * Class StoreConfigurationController.
 */
class StoreConfigurationController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Configuration\CarouselHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function carousel(CarouselHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Configuration\ConfigurationHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function configuration(ConfigurationHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\Configuration\SettingsHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function settings(SettingsHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
