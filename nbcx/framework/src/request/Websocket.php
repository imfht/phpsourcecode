<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\request;
use nb\Pool;

/**
 * Websocket
 *
 * @package nb\request
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2018/10/15
 */
class Websocket extends Base {

    /**
     * @var \swoole\websocket\Frame
     */
    protected $frame;

    public function __construct() {
        $this->frame = Pool::get('\swoole\websocket\Frame');
        $this->frame or $this->frame = new \StdClass();
    }

    public function _data() {
        return $this->frame->data;
    }

    public function _fd() {
        return $this->frame->fd;
    }

}