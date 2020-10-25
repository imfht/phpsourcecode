<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * 控制器基类
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/7/25
 *
 * @property  View view
 *
 * @method display()
 * @method assign()
 * @method formx(...$args)
 * @method form(...$args)
 * @method input(...$args)
 */
class Controller extends Component {

    public function __construct() {
        $this->driver = static::create($this);
    }

    /**
     * 创建并返回一个驱动对象
     * 此函数创建的对象，非单列对象
     *
     * @return \nb\dao\Driver
     */
    public static function create(...$args) {

        $config = static::config();
        $class = static::parse(get_class(),$config);

        return new $class($args[0]);
    }

    /**
     * 设置当参数验证失败时的回调函数
     * @param $args 验证失败的参数名称
     * @param $msg 失败原因
     */
    public function __error($msg,$args) {
        Pool::object('nb\\event\\Framework')->validate(
            $msg,
            $args
        );
    }


}