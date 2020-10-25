<?php

namespace App\Controllers;

use Nimble\Validator\Validator;

class Controller
{
    /**
     * @var Nimble\Foundation\Container
     */
    protected $container;

    /**
     * @var Nimble\Http\Request
     */
    protected $request;

    /**
     * @var Nimble\Http\Response
     */
    protected $response;

    /**
     * @param  Nimble\Foundation\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->request = $container->request;
        $this->response = $container->response;

        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }

        $this->enableCrossRequest();
    }

    private function enableCrossRequest()
    {
        $origin = $this->request->header('origin');
        $this->setHeader('Access-Control-Allow-Origin', $origin);
        $this->setHeader('Access-Control-Allow-Credentials', 'true');
        $this->setHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, X-Requested-With, Cache-Control, Authorization');
        $this->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    }

    /**
     * @param  string $key
     * @param  string $value
     */
    protected function setHeader($key, $value)
    {
        $this->container->response->header($key, $value);
    }
    
    /**
     * @param  string $name
     * @param  string $value
     * @param  int    $expire
     * @param  string $path
     * @param  string $domain
     * @param  bool   $secure
     * @param  bool   $httpOnly
     *
     * @return bool
     */
    protected function cookie($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        return $this->container->response->cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }
    
    /**
     * @param  string $tpl
     * @param  array  $vars
     *
     * @return string
     */
    protected function view($tpl, array $vars = [])
    {
        return $this->container->view->display($tpl, $vars);
    }
    
    protected function outputJson($data = [], $errno = 0, $message = 'success')
    {
        $this->setHeader("Content-Type", "application/json");
        return json_encode([
            'errno'   => $errno,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function validator(array $validReg, array $validMsg = [])
    {
        if (!isset($validMsg['require'])) {
            $validMsg['require'] = ':attribute can not be empty';
        }
        return Validator::make($validReg, $validMsg);
    }
}

