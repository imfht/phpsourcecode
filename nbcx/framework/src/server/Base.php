<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\server;

/**
 * Base
 *
 * @package nb\server
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/28
 */
class Base extends Driver {


    protected $config = [
        'host'=>'127.0.0.1',
        'port'=>8000,
        'root'=>__APP__ . 'public'
    ];

    public function __construct($config=[]) {
        if(is_array($config)) {
            $this->config = array_merge(
                $this->config,
                $config
            );
        }
    }

    public function start($daemonize=true) {
        $host = $this->config['host'];
        $port = $this->config['port'];
        $root = escapeshellarg($this->config['root']);

        $command = sprintf(
            'php -S %s:%d -t %s',// %s
            $host,
            $port,
            $root
        );
        $output = new \nb\console\output\Output();
        $output->writeln(sprintf('NB Development server is started On <<info>http://%s:%s</info>>', $host, $port));
        $output->writeln(sprintf('You can exit with <info>`CTRL-C`</info>'));
        $output->writeln(sprintf('Document root is: %s', $root));
        passthru($command);
    }

    public function restart() {
        echo 'This mode does not support.';
    }

    public function stop() {
        echo 'This mode does not support.';
    }

    public function status() {
        echo 'This mode does not support.';
    }

    public function reload() {
        echo 'This mode does not support.';
    }

}
