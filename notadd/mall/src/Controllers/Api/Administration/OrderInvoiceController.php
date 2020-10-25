<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-08 17:53
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Order\Invoice\CreateHandler;
use Notadd\Mall\Handlers\Administration\Order\Invoice\EditHandler;
use Notadd\Mall\Handlers\Administration\Order\Invoice\InvoiceHandler;
use Notadd\Mall\Handlers\Administration\Order\Invoice\ListHandler;
use Notadd\Mall\Handlers\Administration\Order\Invoice\RemoveHandler;

/**
 * Class OrderInvoiceController.
 */
class OrderInvoiceController extends Controller
{
    /**
     * @var array
     */
    protected $permissions = [
        'global::mall-administration::order-invoice::create'  => 'create',
        'global::mall-administration::order-invoice::edit'    => 'edit',
        'global::mall-administration::order-invoice::invoice' => 'invoice',
        'global::mall-administration::order-invoice::list'    => 'list',
        'global::mall-administration::order-invoice::remove'  => 'remove',
    ];

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Invoice\CreateHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function create(CreateHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Invoice\EditHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function edit(EditHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Invoice\InvoiceHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function invoice(InvoiceHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Invoice\ListHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function list(ListHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\Administration\Order\Invoice\RemoveHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function remove(RemoveHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
