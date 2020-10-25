<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\cache;


class Memcache extends Memcached {

    protected $options = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'expire' => 0,
        'timeout' => 0, // 超时时间（单位：毫秒）
        'persistent' => true,
        'prefix' => '',
    ];

    /**
     * @var \Memcache
     */
    protected $handler;

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     * @throws \BadFunctionCallException
     */
    public function __construct($options = []) {
        if (!extension_loaded('memcache')) {
            throw new \BadFunctionCallException('not support: memcache');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Memcache;
        // 支持集群
        $hosts = explode(',', $this->options['host']);
        $ports = explode(',', $this->options['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        foreach ((array)$hosts as $i => $host) {
            $port = isset($ports[$i]) ? $ports[$i] : $ports[0];
            $this->options['timeout'] > 0 ?
                $this->handler->addServer($host, $port, $this->options['persistent'], 1, $this->options['timeout']) :
                $this->handler->addServer($host, $port, $this->options['persistent'], 1);
        }
    }

}
