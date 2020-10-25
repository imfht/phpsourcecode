<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\dao;

use nb\Pool;

/**
 * Sqlite
 *
 * @package nb\dao
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/27
 */
class Sqlite extends Driver {

    protected $server = [
        'user' 		=> null,
        'pass' 		=> null,
        'object'    => false,
    ];

    /**
     * 根据参数$option初始化PDO
     * @var Pdo
     */
    public function _db(){
        $server = $this->server;

        $dsn = "{$server['driver']}:{$server['dbname']}";

        return Pool::object($dsn,'\\PDO',[
            $dsn,$server['user'],$server['pass']
        ]);
    }

}