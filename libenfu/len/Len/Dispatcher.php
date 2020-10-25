<?php

use Input\Get;
use Input\Post;
use Input\Put;
use Input\Argv;

class Dispatcher
{
    private $router;

    private $public_config;

    private $control;

    private $action;

    /**
     * @var Get
     */
    private $_get;

    /**
     * @var Argv
     */
    private $_argv;

    /**
     * @var Post
     */
    private $_post;

    /**
     * @var Put
     */
    private $_put;

    /**
     * @return Argv
     */
    public function argv()
    {
        return $this->_argv;
    }

    /**
     * @param Argv $_argv
     */
    public function setArgv(Argv $_argv)
    {
        $this->_argv = $_argv;
    }

    /**
     * @return Get
     */
    public function get()
    {
        return $this->_get;
    }

    /**
     * @param Get $get
     */
    public function setGet(Get $_get)
    {
        $this->_get = $_get;
    }

    /**
     * @return put
     */
    public function put()
    {
        return $this->_put;
    }

    /**
     * @param Put $_put
     */
    public function setPut(Put $_put)
    {
        $this->_put = $_put;
    }

    /**
     * @return Post
     */
    public function post()
    {
        return $this->_post;
    }

    /**
     * @param Post $_post
     */
    public function setPost(Post $_post)
    {
        $this->_post = $_post;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getControl()
    {
        return ucfirst($this->control);
    }

    /**
     * @param $control
     */
    public function setControl($control)
    {
        $this->control = $control;
    }

    /**
     * @return router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param router $router
     */
    public function setRouter(router $router)
    {
        $this->router = $router;
    }

    /**
     * @return mixed
     */
    public function getPublicConfig()
    {
        return $this->public_config;
    }

    /**
     * @param $public_config
     */
    public function setPublicConfig($public_config)
    {
        $this->public_config = $public_config;
    }

    /**
     * @param \Router $router
     * @param $public_config
     * @return \Dispatcher
     */
    public static function instance(\Router $router, $public_config)
    {
        return new self($router, $public_config);
    }

    /**
     * dispatcher constructor.
     * @param router $router
     * @param $public_config
     */
    private function __construct(\Router $router, $public_config)
    {
        $this->setRouter($router);
        $this->setPublicConfig($public_config);
        $this->setGet(new Get());
        $this->setPost(new Post());
        $this->setPut(new Put());
        $this->setArgv(new Argv());
        $this->_dispatcher();
        $this->run();
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        $Router = $this->getRouter();
        $control_name = $this->getControl() ?: $Router->control;
        if (empty($control_name)) {
            throw new \Exception('no controller used . ', 404);
        }
        $control_file = CONTROLLER_DIR . $control_name . EXT;
        if (!file_exists($control_file)) {
            throw new \Exception('controller does not exist', 404);
        }
        include $control_file;
        if (!class_exists($control_name) && !interface_exists($control_name)) {
            throw new \Exception('no controller used', 404);
        }
        $controller = new $control_name($this);
        $action_name = $this->getAction() ?: $Router->action;
        if (!method_exists($controller, $action_name)) {
            throw new \Exception('no action used', 404);
        }

        $controller->$action_name();
    }

    /**
     * @throws \Exception
     */
    private function _dispatcher()
    {
        $control_name = $this->get()->getOne('control');
        $action_name = $this->get()->getOne('action');
        if (IS_CLI) {
            $control_name = $this->argv()->getOne('control');
            $action_name = $this->argv()->getOne('action');
        }
        $control_name = urldecode($control_name);
        $action_name = urldecode($action_name);

        $pattern = '/^[a-zA-Z0-9_]{1,256}$/';
        if (!preg_match($pattern, $control_name) && $control_name) {
            throw new \Exception('invalid controller name:' . json_encode($_SERVER));
        }
        if (!preg_match($pattern, $action_name) && $action_name) {
            throw new \Exception('invalid action name' . json_encode($_SERVER));
        }

        $this->setControl($control_name);
        $this->setAction($action_name);
    }
}