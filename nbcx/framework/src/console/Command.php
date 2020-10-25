<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\console;

use nb\console\input\Input;
use nb\console\output\Output;

/**
 * 命令类接口
 *
 * @package nb\console
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/7/26
 */
interface Command {

    /**
     * 配置指令
     */
    function configure(Pack $cmd);

    /**
     * 执行指令
     * @param Input $input
     * @param Output $output
     * @return null|int
     * @throws \LogicException
     * @see setCode()
     */
    function execute(Input $input, Output $output);

    /**
     * 用户验证
     * @param Input $input
     * @param Output $output
     */
    //function interact(Input $input, Output $output);

    /**
     * 初始化
     * @param Input $input An InputInterface instance
     * @param Output $output An OutputInterface instance
     */
    //function initialize(Input $input, Output $output);
}