<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-04-24 18:41
 */
namespace Notadd\Mall\Controllers\Api\Administration;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\Administration\Upload\UploadHandler;

/**
 * Class UploadController.
 */
class UploadController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\Administration\Upload\UploadHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function handle(UploadHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
