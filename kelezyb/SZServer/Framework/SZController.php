<?php
namespace Framework;

/**
 * Class SZController
 * @package Framework
 * @author kelezyb
 * @version 0.9.0.1
 */
class SZController {
    protected $fd;

    public function __construct($fd) {
        $this->fd = $fd;
    }
}