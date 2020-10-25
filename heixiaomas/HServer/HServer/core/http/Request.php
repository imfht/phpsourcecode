<?php

namespace HServer\core\http;

class Request
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var array
     */
    protected $get;

    /**
     * @var array
     */
    protected $files;

    /**
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var string
     */
    protected $fullRequestUri;

    /**
     * @var string
     */
    protected $ip;


    /**
     * @var null|object
     */
    protected $controllerInfo = null;


    public function __construct($data)
    {
        $this->headers = $data['server'];
        $this->method = strtoupper($this->headers['REQUEST_METHOD']);
        // Parsing get parameters
        foreach ($data['get'] as $key => $value) {
            $this->get[$key] = $value;
        }
        // Parsing post parameters
        foreach ($data['post'] as $key => $value) {
            $this->post[$key] = $value;
        }

        // Parsing files
        $this->files = [];
        foreach ($data['files'] as $fileinfo) {
            array_push($this->files, new File($fileinfo));
        }

        $this->hostname = $this->headers['HTTP_HOST'];
        $this->fullRequestUri = $this->headers['REQUEST_URI'];
        $this->requestUri = $this->headers['REQUEST_URI'];
        if (!!strpos($this->requestUri, "?")) {
            $this->requestUri = strtolower(substr($this->requestUri, 0, strpos($this->requestUri, "?")));
        }
        $this->ip = $this->headers['REMOTE_ADDR'];
    }

    public function get($key = null, $value = null)
    {
        if (is_null($key)) {
            return (object)$this->get;
        }
        if (is_null($value)) {
            return isset($this->get[$key]) ? $this->get[$key] : null;
        } else {
            $this->get[$key] = $value;
        }
    }

    public function post($key = null, $value = null)
    {
        if (is_null($key)) {
            return (object)$this->post;
        }
        if (is_null($value)) {
            return isset($this->post[$key]) ? $this->post[$key] : null;
        } else {
            $this->post[$key] = $value;
        }
    }


    public function getIp()
    {
        return $this->ip;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function getUri()
    {
        return $this->requestUri;
    }

    public function getFullUri()
    {
        return $this->fullRequestUri;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getMethod()
    {
        return $this->method;
    }


    /**
     * Get a posted file or all
     *
     * @param string|null $name
     * @return array|File|null
     */
    public function file($name = null)
    {
        if (is_null($name)) {
            return $this->files;
        } else {
            foreach ($this->files as $file) {
                if ($file->getName() === $name) {
                    return $file;
                }
            }
        }
        return null;
    }

    /**
     * @return null|object
     */
    public function getControllerInfo()
    {
        return $this->controllerInfo;
    }

    /**
     * @param null|object $controllerInfo
     */
    public function setControllerInfo($controllerInfo)
    {
        $this->controllerInfo = $controllerInfo;
    }

    /**
     * @return string
     */
    public function getFullRequestUri()
    {
        return $this->fullRequestUri;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }




}