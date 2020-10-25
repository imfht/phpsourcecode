<?php

use Input\Get;
use Input\Post;
use Input\Put;
use Input\Argv;

abstract class BaseControl
{
    /**
     * @var \Dispatcher
     */
    private $dispatcher;

    /**
     * @var Get
     */
    public $get;

    /**
     * @var Post
     */
    public $post;

    /**
     * @var Put
     */
    public $put;

    /**
     * @var Argv
     */
    public $argv;

    /**
     * @var array
     */
    private $config;

    /**
     * BaseCtrl constructor.
     * @param \Dispatcher $dispatcher
     */
    public final function __construct(\Dispatcher $dispatcher)
    {
        $this->setDispatcher($dispatcher);
        $this->setConfig($dispatcher->getPublicConfig());
        $this->setGet($dispatcher->get());
        $this->setPost($dispatcher->post());
        $this->setPut($dispatcher->put());
        $this->setArgv($dispatcher->argv());
        $this->__initialize();
    }

    public function __initialize(){}

    /**
     * @param $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return empty($this->config[$key]) ? [] : $this->config[$key];
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return \Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param \Dispatcher $dispatcher
     */
    public function setDispatcher(\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Get $get
     */
    private function setGet(get $get)
    {
        $this->get = $get;
    }

    /**
     * @param Post $post
     */
    private function setPost(post $post)
    {
        $this->post = $post;
    }

    /**
     * @param Put $put
     */
    private function setPut(put $put)
    {
        $this->put = $put;
    }

    /**
     * @param Argv $argv
     */
    private function setArgv(argv $argv)
    {
        $this->argv = $argv;
    }

    /**
     * @param $method
     * @return Get|Post|Argv|Put
     */
    public function input($method)
    {
        switch (strtolower($method)) {
            case 'get':
                return $this->get;
            case 'post':
                return $this->post;
            case 'put':
                return $this->put;
            case 'argv':
                return $this->argv;
        }
    }

}
