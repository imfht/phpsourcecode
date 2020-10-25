<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace  nb\server;

/**
 * Driver
 *
 * @package nb\server
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
abstract class Driver {



    abstract public function start($daemonize=true);

    abstract public function restart();

    abstract public function stop();

    abstract public function status();

    abstract public function reload();

    /**
     * 显示帮助信息
     */
    //public function help() {
    //    echo "usage: php -q server.php [restart|start|stop|reload|status]\n";
    //}

}