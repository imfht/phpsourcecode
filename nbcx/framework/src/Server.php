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

use nb\console\Command;
use nb\console\input\Argument;
use nb\console\input\Input;
use nb\console\input\Option;
use nb\console\output\Output;
use nb\console\Pack;

/**
 * Server
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Server extends Component implements Command {

    public static function config() {
        $conf = Config::$o->server;
        return $conf;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Pack $cmd) {
        $cmd->setName('server')
            ->addArgument('act', Argument::OPTIONAL, "[restart|start|stop|reload|status]")
            ->addOption('daemonize', 'd', Option::VALUE_REQUIRED, 'Use daemonize run')
            ->setDescription('run server for http,tcp,udp,websocket,php in config');
            //->setHelp('xxx');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Input $input, Output $output,Pack &$cmd=null) {
        $command = trim($input->getArgument('act'));
        if(!$command) {
            $output->describe($cmd);
            return 0;
        }
        if ($input->hasOption('daemonize')) {
            $daemonize = true;
        }
        else {
            $daemonize = false;
        }
        $driver = self::driver();
        Pool::solidify(self::class);//server对象不应被销毁
        switch ($command) {
            case 'start':
                $driver->start($daemonize);
                break;
            case 'stop':
                $driver->stop();
                break;
            case 'status':
                $driver->status();
                break;
            case 'reload':
                $driver->reload();
                break;
            case 'restart':
                $driver->restart();
                break;
            default:
                //$output->renderException(new \Exception('命令不存在哦'));
                //$output->ask($input,'请输入你的姓名：');
                $output->highlight('The command does not exist，view help：-h');
        }
        return 0;
    }


    /**
     * 用户验证
     * @param Input $input
     * @param Output $output
     */
    function interact(Input $input, Output $output){}

    /**
     * 初始化
     * @param Input $input An InputInterface instance
     * @param Output $output An OutputInterface instance
     */
    function initialize(Input $input, Output $output){}
}