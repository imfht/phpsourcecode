<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-20 17:10
 */
namespace Notadd\Mall\Controllers\Api\User;

use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Mall\Handlers\User\LoginHandler;
use Notadd\Mall\Handlers\User\RegisterHandler;
use Notadd\Mall\Handlers\User\UserHandler;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @param \Notadd\Mall\Handlers\User\LoginHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function login(LoginHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }

    /**
     * @param \Notadd\Mall\Handlers\User\RegisterHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function register(RegisterHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
    
    /**
     * @param \Notadd\Mall\Handlers\User\UserHandler $handler
     *
     * @return \Notadd\Foundation\Routing\Responses\ApiResponse|\Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     * @throws \Exception
     */
    public function user(UserHandler $handler)
    {
        return $handler->toResponse()->generateHttpResponse();
    }
}
