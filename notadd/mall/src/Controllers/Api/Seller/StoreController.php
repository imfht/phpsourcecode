<?php
/**
 * This file is part of Notadd.
 *
 * @author        TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime      2017-05-23 17:06
 */
namespace Notadd\Mall\Controllers\Api\Seller;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Seller\Store\RenewHandler;
use Notadd\Mall\Handlers\Seller\Store\StoreHandler;

/**
 * Class StoreController.
 */
class StoreController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-seller::store::renew' => 'renew',
        'global::mall-seller::store::store' => 'store',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\RenewHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function renew(RenewHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Seller\Store\StoreHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function store(StoreHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
