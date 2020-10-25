<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\dispatcher;

use nb\Config;
use nb\console\input\Argument;
use nb\console\input\Input;
use nb\console\input\Option;
use nb\console\output\Output;
use nb\console\Pack;
use nb\console\Command as ICommand;
use nb\Debug;
use nb\Router;
use nb\Pool;

/**
 * Command
 *
 * @package nb\dispatcher
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Command extends Driver implements ICommand {

    protected $pathinfo;

    /**
     * @var Input
     */
    protected $in;

    public function configure(Pack $cmd) {
        $cmd->setName('action')
            ->addArgument('router', Argument::OPTIONAL, "controller")
            ->addArgument('args', Argument::IS_ARRAY, "parameter list of controller method")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('execute controller in cli');
    }

    public function execute(Input $input, Output $output) {
        Debug::start();
        $this->in = $input;
        $this->pathinfo = trim($input->getArgument('router'));
        $this->run();
    }

    protected function input(\ReflectionClass $controller, $app) {
        if($this->in) {
            return $this->in->getArgument('args');
        }
        return [];
    }

    public function run() {
        $router = Router::driver();
        $this->pathinfo and $router->pathinfo = $this->pathinfo;
        $router = $router->mustAnalyse();

        //如果访问的模块，加载模块配置
        $router->module and $this->module($router->module);

        ///如果加载不成功，作为404处理
        $class = $router->class;
        if(!$class) {
            return Pool::object('nb\event\Framework')->notfound();
        }
        //过滤掉禁止访问的方法
        if (in_array($router->function,Config::$o->notFunc)) {
            return Pool::object('nb\event\Framework')->notfound();
        }
        $this->go($class,$router->function);
    }

    function interact(Input $input, Output $output){}

    /**
     * 初始化
     * @param Input $input An InputInterface instance
     * @param Output $output An OutputInterface instance
     */
    function initialize(Input $input, Output $output){}

}